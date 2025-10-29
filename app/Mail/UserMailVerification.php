<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserMailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $user
     * @param  string  $url
     */
    public function __construct($user, string $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(__('E-Mail bestätigen'))
                    ->view('emails.verify.user-mail-verification')
                    ->with([
                        'user' => $this->user,
                        'url' => $this->url,
                    ]);
    }
}

