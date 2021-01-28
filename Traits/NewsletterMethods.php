<?php

namespace App\Traits;

use Illuminate\Http\Request;

/*
 *  Methods for Newsletter and Newsletterpreview  (or other models)
 */
trait NewsletterMethods{
    
       
    public function getBoxesAttribute()
    {
        return $this->getSizes('boxes', 'boxes', 'data', 'newsletters');
            
    }
    public function getHeaderAttribute()
    {
        return $this->getSizes('headers', 'header', 'header','newsletters');
            
    }

    public function getNewsletterTemplateData(){

        $viewName = $this->template->view;

        $boxes = collect($this->data['boxes'])->groupBy('group'); 
        $color_scheme = $this->data['color_scheme'];  
        $footer = $this->data['footer'];

        //booleans 
        $title_positioned_on_header_image = $this->data['title_positioned_on_header_image'];  
        $append_mailinglist_title_to_mail_subject = $this->data['append_mailinglist_title_to_mail_subject'];  
         
        $withDataArray = [ 
            'boxes'         => $boxes, 
            'color_scheme'  => $color_scheme,
            'newsletter'    => $this,
            'footer'        => (object) $footer, 
            'title_positioned_on_header_image' => $title_positioned_on_header_image, 
            'append_mailinglist_title_to_mail_subject' => $append_mailinglist_title_to_mail_subject, 
        ];

        return [
            'withDataArray' => $withDataArray, 
            'viewName' => $viewName
        ];
    }

    /**
     * Prepare data for the 'data' column. 
     * Fill the data using post request data
     *
     * @param Request $request
     * @return void
     */
    public function getDataForSave(Request $request){
         
        return collect([
            'color_scheme' => $request->get('color_scheme', null),
            'boxes' => collect($request->get('boxes', [])),
            'footer' =>  $request->get('footer', null),
            'mail_subject' =>  $request->get('mail_subject', null),
            'append_mailinglist_title_to_mail_subject' =>  $request->get('append_mailinglist_title_to_mail_subject', null),
            'title_positioned_on_header_image' =>  $request->get('title_positioned_on_header_image', null)
        ]);
    }
    
}