<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $post;
    private $worker;
    private $newProposal;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $user,
        $post,
        $worker,
        $newProposal
    ) {
        $this->user = $user;
        $this->post = $post;
        $this->worker = $worker;
        $this->newProposal = $newProposal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'emails.notifications.proposal',
            [
                'user' => $this->user,
                'post' => $this->post,
                'worker' => $this->worker,
                'newProposal' => $this->newProposal
            ]
        )->subject('New Job Proposal');
    }
}
