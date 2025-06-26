<?php

namespace App\Http\Controllers;

use App\Events\PaymentEvent;
use App\Exports\RegisteredExport;
use App\Exports\StudentExport;
use App\Models\Payments;
use App\Models\PaystackResponse;
use App\Models\Student;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Matscode\Paystack\Paystack;
use Yajra\DataTables\DataTables;

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
        $students = Student::query()->where('payment_complete', '=', true)->orWhere('balance', '!=', null)->get();
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
        // return DataTables::of($students)            
        //     ->make(true);
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

    public function exportRegistered()
    {
        $export = new RegisteredExport();
        return Excel::download($export, 'registered.xlsx');
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

                $student->update([
                    'is_active' => true
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
                        'is_active' => true,
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

    public function generateReg()
    {
        $err = [];
        $list = [];
        try {
            //code...
            $students = Student::query()->where('matric_no', '!=', null)->get();

            // dd($natrics);



            foreach ($students as $key => $student) {
                // dd($key);
                $student->matric_no = $this->reg_number($key + 1);
                $student->save();
                array_push($list, $student->matric_no);
            }
        } catch (\Throwable $th) {
            //throw $th;
            array_push($err, $th->getMessage());

        }
        return response()->json([
            'students' => $students,
            "err" => $err,
            "list" => $list
        ], 200);

    }

    public function generateGroup()
    {
        $err = [];
        try {
            //code...
            $online = Student::query()->where('payment_complete', '=', true)->orWhere('balance', '!=', null)->where('participation_mode', 'online')->where('group_no', null)->get();
            $onsite = Student::query()->where('payment_complete', '=', true)->orWhere('balance', '!=', null)->where('participation_mode', 'onsite')->where('group_no', null)->get();

            // $online = $students->where('participation_mode', 'online')->get();
            // $onsite = $students->where('participation_mode', 'onsite')->get();

            // dd($onsite);


            foreach ($online as $key => $student) {
                $student->group_no = rand(1, 6);
                $student->save();

            }

            foreach ($onsite as $key => $student) {
                $student->group_no = rand(1, 6);
                $student->save();

            }
        } catch (\Throwable $th) {
            //throw $th;
            array_push($err, $th->getMessage());

        }
        return response()->json([
            'online' => $online,
            'onsite' => $onsite,
            "err" => $err
        ], 200);
    }

    public function reg_number($id)
    {
        $regNum = '';
        $uniqueId = str_pad($id, 4, '0', STR_PAD_LEFT);
        $date = 2025;
        $regNum = "ISM" . "/" . $date . "/" . $uniqueId;
        // dd($regNum);
        return $regNum;
    }

    public function getTranscript(Request $request)
    {
        $student = auth()->user();
        if ($student) {
            return response()->json([
                "status" => "success",
                "transcript" => $student->transcript()
            ], 200);
        }
        return response()->json([
            "message" => "student not found"
        ], 404);
    }
}
