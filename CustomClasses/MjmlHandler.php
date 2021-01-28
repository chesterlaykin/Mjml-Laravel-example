<?php

namespace App\CustomClasses;

use App\User;
use App\Newsletter as NewsletterModel;
/*
 * Handles things related to Mjml
 */
class MjmlHandler{

    /*
     * Post request to local node express webserver api endpoint,
        to convert mjml into html (Mjml version 4)  
     * ( See mjml4/index.js )
     */
    public static function postToNodeExpressConvertMjml($mjml){
         
        $port = config('project.node_express_port');
        
        if(!$port || empty($port)){
             return ['error' => 'Node port missing']; 
        }
        
        $url = "http://localhost:$port/mjml";

        $ch = curl_init();

        $options = [
                CURLOPT_HTTPHEADER      => [
                'Content-Type: application/json' 
            ],
            CURLOPT_POST            => 1,
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
            CURLOPT_POSTFIELDS      => $mjml
        ];
        
        curl_setopt_array($ch, $options);
         
        $response = curl_exec($ch);
        
        /* Check for 404 (file not found) or 500. */
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if($httpCode == 404) {
            return ['error' => '404 - Object not found ']; 
            
        }else if($httpCode == 500){
             
            return ['error' => $response]; 
        }
        
        if(!$response){
           return ['error' => 'No response - node server may be down.' . $response]; 
        }
  
       
        curl_close($ch);
         
        return $response;
    }
    
     
    /*
     * Do curl post request to Mjml api (Mjml version 3)
     * Takes api key, password and mjml data
     */
    public static function postMjmlApi($login,$password,$url,$mjml){
        
        //Post request to Mjml api to get html out of mjml
        $ch = curl_init();

        $options = [
            CURLOPT_POST            => 1,
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
            CURLOPT_USERPWD         => "$login:$password",
            CURLOPT_POSTFIELDS      => $mjml,
        ];
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
    }

   /*
    * Converts blade template into mjml.
    * Sends mjml to Node express server to convert to html.
    * Returns html.

    NewsletterModel $newsletter,$mailinglist
    */
   public static function renderHtml($viewName,$withDataArray) {
        
   
       //get mjml out of blade mjml template
       $renderedTemplate = view($viewName)       // a blade view, ex: emails.templates.mjml.template1      
            ->with( $withDataArray )
            ->render();
        
       //convert to html
       $renderedTemplate = str_replace("\r\n",'',$renderedTemplate ); 

       $mjml = json_encode(['mjml' => $renderedTemplate]);
      
       $result = self::postToNodeExpressConvertMjml($mjml);

       //If returned as array, it means error
       if(is_array($result)) {
            //The array should have property 'error'
            
           abort(500,'Error: ' . $result['error']);
       }
       
       return $result;
   }
}

