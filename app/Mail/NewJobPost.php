<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewJobPost extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $post;
    private $owner;
    private $locations;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $post, $owner)
    {
        $this->user = $user;
        $this->post = $post;
        $this->owner = $owner;
        $this->locations = unserialize($post->locations);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'emails.notifications.post',
            [
                'user' => $this->user,
                'post' => $this->post,
                'owner' => $this->owner,
                'locations' => $this->locations
            ]
        )->subject('New Job Post');
    }
}
