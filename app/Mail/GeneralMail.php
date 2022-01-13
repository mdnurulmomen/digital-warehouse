<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ApplicationSetting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneralMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        
        /*
        $generalSettings = ApplicationSetting::firstOrCreate([
            'id' => 1
        ]);
        */

        // $officialMailAddress =  $generalSettings->official_mail_address ?? 'admin@gmail.com';     
        // $officialMailTemplate =  $generalSettings->mail_template;     
        
        /*
        $officialMailAddress =  'nothing@gudam.com.bd';     


        return $this->subject('Test Subject Updated')
            ->view('emails.general');
        */
       
        $generalSettings = ApplicationSetting::firstOrCreate([
            'id' => 1
        ]);
 
        // $officialMailFooter =  $generalSettings->official_mail_footer;     
       
        return $this->subject($this->request['subject'])
            ->markdown('emails.general')
            ->with([
                'appName' => $generalSettings->app_name,
                'officialMediaItems' => $generalSettings->officialMediaItems,
                'officialCopyrightMessage' => $generalSettings->officialCopyrightMessage,
                'officialContactAddress' => $generalSettings->officialContactAddress,
                // 'officialMailFooter' => $officialMailFooter,
            ]);
    }
}
