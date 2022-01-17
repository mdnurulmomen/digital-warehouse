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

    public $fromAddress, $senderName, $subject, $body, $generalSettings;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $generalSettings, $fromAddress = false, $senderName = false)
    {
        $this->body = $body;
        $this->subject = $subject;
        $this->generalSettings = $generalSettings;
        $this->senderName = $senderName ? $senderName : config('mail.from.name');
        $this->fromAddress = $fromAddress ? $fromAddress : config('mail.from.address'); 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {  
        return $this->from($this->fromAddress, $this->senderName)
            ->subject($this->subject)
            ->view('emails.general');
    }
}
