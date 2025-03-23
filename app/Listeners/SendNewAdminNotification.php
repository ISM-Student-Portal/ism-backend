<?php

namespace App\Listeners;

use App\Events\NewAdminEvent;
use App\Mail\NewAdmin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendNewAdminNotification
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
    public function handle(NewAdminEvent $event): void
    {
        //
        $mail = new NewAdmin($event->user, $event->password);
        
        Mail::to($event->user)->send($mail);
    }
}
