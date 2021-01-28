<?php

namespace App;

use App\Jobs\SendEmail;
use App\NewsletterTemplate;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

use App\Mail\Newsletter as NewsletterMail;

use App\Traits\NewsletterMethods;

use Illuminate\Http\Request;

class Newsletter extends Model
{
     
    use NewsletterMethods;
     
    /**
     * The accessors and mutators to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'boxes','header','mailinglist_ids'];       
     
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];
 
    public function mailinglists(){
        
        return $this->belongsToMany(
            'App\Mailinglist',
            'newsletter_mailinglist',
            'newsletter_id',
            'mailinglist_id'
        ); 
    }

    public function template()
    {
        return $this->belongsTo(NewsletterTemplate::class, 'newsletter_template_id', 'id');
    }
 


    /**
     * Save Newsletter
     * 
     * Save the current newsletter instance (may me existing or newly instantiated)
     *
     * @param Request $request
     * @return object
     */
    public function saveNewsletter(Request $request,$isDraft,$isSending){
         

        $data = $this->getDataForSave($request);
       
        $this->title = $request->title;
        $this->data = $data;
        $this->newsletter_template_id = $request->get('template')['id'];

        if ($request->filled('association_id')) {
            $this->association_id = $request->get('association_id');
        }

        $this->header = $request->header ? $request->header : null;

        $this->draft = $isDraft && !$isSending ? 1 : 0;
      
        if ($isSending) {
            $this->sent_at = now();
        } 
        $this->save();

        $this->mailinglists()->sync($request->mailinglist_ids);
 
    }

    /**
     * Retrieves data for the current mailinglist
     * 
     * Sends current newsletter to all its connected mailinglists
     *
     * @return void
     */
    public function dispatchNewsletterToMailinglists(){
        
        //get initial data (everything except subject and mailinglist)
        $data = $this->getNewsletterTemplateData();
        
        //Dispatch each newsletter in turn
        $counts = [];
        foreach($this->mailinglists as $list){

            //Add subject and current mailinglist to data

            $subject = $data['mail_subject'] ?? $this->title;
            $subject = $data['append_mailinglist_title_to_mail_subject'] == 1 ? sprintf('%s - %s', $subject, $list->getName()) : $subject;
            $data['subject'] = $subject;

            //Add mailing list to data
            $data['withDataArray']['mailinglist'] = $list;

            $count = $this->sendNewsletterToList($this,$list,$data);
            $counts[$list->getName()] = $count;
        }
        $nyhetsbrev = count($this->mailinglists->toArray()) == 1 ? 'Nyhetsbrevet' : 'Nyhetsbreven';

        return [
            'nyhetsbrev' => $nyhetsbrev,
            'counts' => $counts, 
        ];
    
    }

    /*
        Takes $newsletter and one of the mailinglists belonging to the newsletter 
        sends mail to all mailing list subscribers of the mailing list
     *  Converts newsletter template with data from blade mjml to html (handled by local node express server).
     */
    public function sendNewsletterToList($mailinglist,$data){
     
        $subscribers = DB::table('mailing_list_subscribers as mls')
            ->join('users as u','mls.user_id','=','u.id')
            ->distinct()
            ->selectRaw('u.email')
            ->where('mls.mailing_list_id',$mailinglist->id)
            ->get();
 
        $subscribers = array_column($subscribers->toArray(),'email');
        
        //remove null values and invalid emails
        $subscribers = collect($subscribers)->filter( function($subscriber){
            return $subscriber !== null && filter_var($subscriber, FILTER_VALIDATE_EMAIL);
        }); 
         
        $mailContent = new NewsletterMail($data);
        
        //Test - local dev
        if( config('app.env') == 'local'){
            $subscribers = collect(['joelsetterberg@hotmail.com','coorhagen78@gmail.com']);
        }

        if($mailContent){
            //Send mail to each subscriber
            foreach($subscribers as $subscriber){

                SendEmail::dispatch($subscriber, $mailContent);
                
            }
            return count($subscribers->toArray());
            
        }else{
            return response()->json(['message' => 'There were errors',
                'errors' => [ 'node_error' => ['Ett node express-relaterat fel uppstod.']]
            ],500);
        }
        
             
    }

  
    

}
