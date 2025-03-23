<?php

namespace App\Listeners;

use App\Events\NewLecturerEvent;
use App\Mail\NewLecturer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendNewLecturerNotification
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
    public function handle(NewLecturerEvent $event): void
    {
        //
        $mail = new NewLecturer($event->lecturer, $event->password);

        Mail::to($event->lecturer)->send($mail);

    }
}
