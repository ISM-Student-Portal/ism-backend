<?php

namespace App\Http\Controllers;

use App\Events\PaymentEvent;
use App\Models\Payments;
use App\Models\Student;
use Illuminate\Http\Request;
use Matscode\Paystack\Paystack;

class StudentController extends Controller
{
    //

    public function show(Request $request)
    {
        $student = Student::where('id', $request->id)->first();

        if ($student) {
            return response()->json([
                "status" => "success",
                "student" => $student
            ], 200);
        }
        return response()->json([
            "message" => "student not found"
        ], 404);
    }

    public function paySubscription(Request $request)
    {
        $student = Student::where('id', $request->id)->first();
        $plan = $request->plan;
        $reference = $request->reference;
        $amount = $request->amount;
        $Paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));

        $expected_amount = $plan == 'basic' ? 100000 : 200000;

        if ($student && $reference) {
            $response = $Paystack->transaction->verify($reference['reference']);
            // dd($response);
            if ($response->status == true) {
                $payment = Payments::create([
                    'student_id' => $student->id,
                    'amount' => $amount,
                    'reference' => $response->data->reference,
                    'status' => $response->data->status,
                    'payment_method' => $response->data->channel,
                    'payment_channel' => $response->data->channel,
                ]);

                $student->update([
                    'payment_complete' => true,
                    'plan' => $plan
                ]);
            } else {
                return response()->json([
                    "message" => "Operation was not successful"
                ], 404);
            }
            event(new PaymentEvent($payment));
            return response()->json([
                "status" => "success",
                "message" => "subscription paid successfully"
            ], 200);
        }
        return response()->json([
            "message" => "Operation was not successful"
        ], 404);
    }
}
