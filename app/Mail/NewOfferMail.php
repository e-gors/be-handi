<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    private $client;
    private $offer;
    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $client,
        $offer,
        $user
    ) {
        $this->client = $client;
        $this->offer = $offer;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'emails.notifications.offer',
            [
                'client' => $this->client,
                'offer' => $this->offer,
                'user' => $this->user
            ]
        )->subject('New Job Offer');
    }
}
