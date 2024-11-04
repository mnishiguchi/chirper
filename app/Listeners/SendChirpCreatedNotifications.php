<?php

namespace App\Listeners;

use App\Events\ChirpCreated;
use App\Models\User;
use App\Notifications\NewChirpNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Subscribe to our ChirpCreated event and send notifications.
 */
class SendChirpCreatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ChirpCreated $event): void
    {
        // User::whereNot is syntactically OK but unfourtunately LSP can't figure it out...
        // We use database cursor to avoid loading every user into memory at once.
        foreach (User::whereNot('id', $event->chirp->user_id)->cursor() as $user) {
            $user->notify(new NewChirpNotification($event->chirp));
        }
    }
}
