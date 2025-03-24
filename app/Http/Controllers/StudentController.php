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
        $students = Student::query()->whereHas('payments')->get();
        return response()->json([
            "status" => "success",
            "students" => $students
        ], 200);
    }

    public function getAllRegistrants()
    {
        $students = Student::query()->get();
        $totalPaid = Student::query()->whereHas('payments')->count();
        $totalUnpaid = Student::query()->whereDoesntHave('payments')->count();
        $totalPaidFull = Student::query()->where('payment_complete', true)->count();
        return response()->json([
            "status" => "success",
            "students" => $students,
            "stats" => [
                "total_paid" => $totalPaid,
                "total_unpaid" => $totalUnpaid,
                "total_paid_full" => $totalPaidFull
            ]
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

        if ($student->balance > 0) {
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
                    'balance' => null
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

        if ($student->country === "Nigeria") {
            $expected_amount = $plan == 'basic' ? 225000 : 375000;
            $expected_amount = $student->is_alumni ? $expected_amount / 2 : $expected_amount;
        } else {
            $expected_amount = $plan == 'basic' ? 150 : 250;
            $expected_amount = $student->is_alumni ? $expected_amount / 2 : $expected_amount;
        }

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

                if ($amount < $expected_amount) {
                    $student->update([
                        'plan' => $plan,
                        'balance' => $expected_amount - $amount
                    ]);
                } else {
                    $student->update([
                        'payment_complete' => true,
                        'plan' => $plan
                    ]);
                }


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
