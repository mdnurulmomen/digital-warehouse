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

    public $subject, $from;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $from = false)
    {
        $this->from = $from;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {  
        $generalSettings = ApplicationSetting::firstOrCreate([
            'id' => 1
        ]);
 
        // $officialMailHeader =  $generalSettings->official_mail_header;     
        // $officialMailFooter =  $generalSettings->official_mail_footer;     
       
        return $this->subject($this->subject)
            ->view('emails.general')
            ->with([
                'appName' => $generalSettings->app_name,
                'officialMediaItems' => $generalSettings->medias,
                'officialCopyrightMessage' => $generalSettings->copyright_message,
                'officialContactAddress' => $generalSettings->official_contact_address,
                'message' => $this
                // 'officialMailHeader' => $officialMailHeader,
                // 'officialMailFooter' => $officialMailFooter,
            ]);
    }
}
