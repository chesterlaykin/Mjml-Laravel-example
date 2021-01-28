<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\CustomClasses\MjmlHandler;

class Newsletter extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = '';
    public $html = '';
    

    /**
     * Create a new message instance.
     * 
     * $data should contain what is retrieved in Newsletter->getNewsletterTemplateData()
     *      plus 'subject' 
     */
    public function __construct($data)
    {
         
        $this->html = MjmlHandler::renderHtml($data['viewName'],$data['withDataArray']);
        $this->subject = $data['subject'];
    }

    /**
     * Build the message.
     * 
     */
    public function build()
    { 

        return $this->view($this->html) 
                    ->subject($this->subject);
    }
}
