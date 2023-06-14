<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    private $worker;
    private $offer;
    private $client;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $worker,
        $offer,
        $client
    ) {
        $this->worker = $worker;
        $this->offer = $offer;
        $this->client = $client;
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
                'worker' => $this->worker,
                'offer' => $this->offer,
                'client' => $this->client
            ]
        )->subject('New Job Offer');
    }
}
