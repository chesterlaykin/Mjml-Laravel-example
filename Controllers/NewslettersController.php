<?php

namespace App\Http\Controllers\Api;

use App\Newsletter; 
use Illuminate\Http\Request; 

use App\Http\Controllers\Controller;


class NewslettersController extends Controller
{
    /**
     * Save newsletter as draft or not draft.
     * If not draft, it will be sent to all mailing list participants.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required'], 
            'mailinglist_ids' => ['required'],
        ]);
        
        $newsletter = new Newsletter;
        $isDraft = $request->filled('draft');
        $isSending = $request->filled('sending');

        $newsletter->saveNewsletter($request,$isDraft,$isSending);
   
        //If sending, send mail to list subscribers of each list
        if ($isSending) {

            $result = $newsletter->dispatchNewsletterToMailinglists(); 
            return response()->json(['model' => $newsletter, 'message' => $result['nyhetsbrev'] . " skickades",'postcounts' => $result['counts'], 'redirect'=>$redirect]);
        
        }else{
            $params = $request->association_id ? [ $request->association_id ,$newsletter] : [$newsletter];
            $redirect = $request->association_id ? route('my.association.newsletters.edit',$params) : route('admin.newsletter',$params);
            return response()->json(['model' => $newsletter, 'message' => 'Nyhetsbrevet har blivit sparat som utkast.','redirect' => $redirect]);
        }
  
          
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Newsletter  $newsletter
     * @return array
     */
    public function update(Request $request, Newsletter $newsletter)
    {
        $this->validate($request, [
            'title' => ['required'],
            'mailinglist_ids' => ['required'],
        ]);
       
        $isDraft = $request->filled('draft');
        $isSending = $request->filled('sending');
         
        $newsletter->saveNewsletter($request,$isDraft,$isSending);
  
        $redirect = $request->association_id ? route('my.association.newsletters',$request->association_id) : route('admin.newsletters');

        if ($isSending) {

            $result = $newsletter->dispatchNewsletterToMailinglists();
             
            return response()->json(['model' => $newsletter, 'message' => $result['nyhetsbrev'] . " skickades",'postcounts' => $result['counts'], 'redirect'=>$redirect]);
        }

        return response()->json(['model' => $newsletter, 'message' => 'Nyhetsbrevet har blivit uppdaterat.']);
         
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return ['message' => 'Nyhetsbrevet har blivit borttaget.'];
    }
    
    
}
