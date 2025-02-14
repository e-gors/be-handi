<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcceptProposal extends Mailable
{
    use Queueable, SerializesModels;

    private $worker;
    private $proposal;
    private $client;
    private $contract;
    private $post;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $worker,
        $proposal,
        $client,
        $contract,
        $post
    ) {
        $this->worker = $worker;
        $this->proposal = $proposal;
        $this->client = $client;
        $this->contract = $contract;
        $this->post = $post;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'emails.notifications.acceptProposal',
            [
                'worker' => $this->worker,
                'proposal' => $this->proposal,
                'client' => $this->client,
                'contract' => $this->contract,
                'post' => $this->post
            ]
        );
    }
}
