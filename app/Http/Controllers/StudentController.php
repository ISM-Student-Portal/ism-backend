<?php

namespace App\Http\Controllers;

use App\Events\PaymentEvent;
use App\Exports\StudentExport;
use App\Models\Payments;
use App\Models\PaystackResponse;
use App\Models\Student;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Matscode\Paystack\Paystack;

class StudentController extends Controller
{
    //

    public function show(Request $request)
    {
        $student = Student::with(['payments'])->where('id', $request->id)->first();

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

    public function getAll()
    {
        $students = Student::query()->get();
        return response()->json([
            "status" => "success",
            "students" => $students
        ], 200);
    }

    public function export()
    {
        $export = new StudentExport();
        return Excel::download($export, 'students.xlsx');
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
            $res = PaystackResponse::create([
                'student_id' => $student->id,
                'response' => Json::encode($response),
            ]);
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
