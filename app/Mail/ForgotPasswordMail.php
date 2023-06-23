<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $link)
    {
        $this->user = $user;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.password.forgot_password', [
            'link' => $this->link,
            'user' => $this->user,
            'expirationTime' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minutes',
        ])->subject('Forgot Password');
    }
}
