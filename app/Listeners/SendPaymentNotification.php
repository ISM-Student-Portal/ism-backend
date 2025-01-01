<?php

namespace App\Listeners;

use App\Events\PaymentEvent;
use App\Mail\PaymentMail;
use App\Models\Student;
use App\Notifications\PaymentMailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentNotification
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
    public function handle(PaymentEvent $event): void
    {
        //
        $payment = $event->payment;
        $student = Student::find($payment->student_id);

        $student->notify(new PaymentMailNotification($payment, $student));
    }
}
