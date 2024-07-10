<?php

namespace App\Listeners;

use App\Events\UserStatusChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserStatusChangedListener implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserStatusChanged $event)
    {
        // Get the updated user's status
        $status = $event->user->status;

        // Update the user's online status in cache
        Cache::put('user-is-online-' . $event->user->id, $status === 'online', now()->addMinutes(5));

        // Log the status update
        Log::info('User status updated: ' . $event->user->fullname . ' (' . $event->user->email . ') is ' . $status);
    }
}
