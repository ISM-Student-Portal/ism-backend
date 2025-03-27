<?php

namespace App\Http\Controllers;

use App\Mail\NewUser;
use App\Mail\PasswordReset;
use App\Models\Admin;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
use Password;
use Validator;

class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $email = $request->input('email');
        if (Student::where('email', $email)->exists()) {
            $student = Student::where('email', $email)->first();
            if (!$student->is_active) {
                return response()->json([
                    "message" => "Account is inactive"
                ], 401);
            }
            $auth = Auth::guard("student");
            if ($auth->attempt(["email" => $student->email, "password" => $request->input("password")]) || $auth->attempt(["username" => $student->username, "password" => $request->input("password")])) {
                $token = auth()->guard('student')->user()->createToken('student');
                return response()->json([
                    "status" => "success",
                    'user' => $student,
                    "token" => $token
                ], 200);
            }
            return response()->json([
                "message" => "invalid email or password"
            ], 401);
        } else if (Admin::where('email', $email)->exists()) {

            $admin = Admin::where('email', $email)->first();
            if (!$admin->is_active) {
                return response()->json([
                    "message" => "Account is inactive"
                ], 401);
            }
            $auth = Auth::guard("admin");
            if ($auth->attempt(["email" => $admin->email, "password" => $request->input("password")]) || $auth->attempt(["username" => $admin->username, "password" => $request->input("password")])) {
                $token = auth()->guard('admin')->user()->createToken('admin');
                return response()->json([
                    "status" => "success",
                    'user' => $admin,
                    "token" => $token
                ], 200);
            }
            return response()->json([
                "message" => "invalid email or password"
            ], 401);
        } else if (Lecturer::where('email', $email)->exists()) {

            $lecturer = Lecturer::where('email', $validated['email'])->first();
            if (!$lecturer->is_active) {
                return response()->json([
                    "message" => "Account is inactive"
                ], 401);
            }
            $auth = Auth::guard("lecturer");
            if ($auth->attempt(["email" => $lecturer->email, "password" => $request->input("password")]) || $auth->attempt(["username" => $lecturer->username, "password" => $request->input("password")])) {
                $token = auth()->guard('lecturer')->user()->createToken('lecturer');
                return response()->json([
                    "status" => "success",
                    'user' => $lecturer,
                    "token" => $token
                ], 200);
            }
            return response()->json([
                "message" => "invalid email or password"
            ], 401);
        }

        return response()->json([
            "message" => "invalid email or password"
        ], 401);
    }

    public function resendEmail(Request $request)
    {
        $user = Student::find($request->id);
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }
        $user->sendEmailVerificationNotification();
        return response()->json(['message' => 'Email sent'], 200);
    }

    public function verifyEmail(Request $request)
    {
        $user = Student::find($request->id);
        if ($user->hasVerifiedEmail()) {

            return redirect(env('FRONTEND_URL') . '/payment/' . $user->id);

            // return response()->json(['message' => 'Email already verified'], 400);
        }
        if ($user->markEmailAsVerified()) {
            return redirect(env('FRONTEND_URL') . '/payment/' . $user->id);
        }
        return response()->json(['message' => 'Email not verified'], 400);
    }

    public function forgotPassword(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                $token = DB::table('password_reset_tokens')->where('email', '=', $request->email);
                if ($token) {
                    $token->delete();
                }
                DB::table('password_reset_tokens')->insert([
                    'email' => $request->email,
                    'token' => Str::random(60),
                    'created_at' => Carbon::now()
                ]);
                $tokenData = DB::table('password_reset_tokens')
                    ->where('email', $request->email)->first();
                if ($this->sendResetEmail($request->email, $tokenData->token)) {
                    return response()->json(['message' => trans('A reset link has been sent to your email address.'), 'status' => 'success'], 200);
                } else {
                    return response()->json(['error' => trans('A Network Error occurred. Please try again.')], 400);
                }
            } catch (Exception $ex) {
                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            }
        }
        return \Response::json($arr);
    }

    private function sendResetEmail($email, $token)
    {
        //Retrieve the user from the database

        if (Student::where('email', $email)->exists()) {
            $student = Student::where('email', $email)->first();

            $user = $student;

        } else if (Admin::where('email', $email)->exists()) {

            $admin = Admin::where('email', $email)->first();
            $user = $admin;
        } else if (Lecturer::where('email', $email)->exists()) {

            $lecturer = Lecturer::where('email', $email)->first();
            $user = $lecturer;
        }

        //Generate, the password reset link. The token generated is embedded in the link
        $link = env('FRONTEND_URL') . '/password/reset?token=' . $token . '&email=' . urlencode($user->email);
        Mail::to($user)->send(new PasswordReset($link, $user));


        try {
            //Here send the link with CURL with an external email API 
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function resetPassword(Request $request)
    {
        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->input('token'))->first();
        if (!$tokenData)
            return response()->json(['status' => 'error'], 400);
        if (Student::where('email', $tokenData->email)->exists()) {
            $student = Student::where('email', $tokenData->email)->first();

            $user = $student;

        } else if (Admin::where('email', $tokenData->email)->exists()) {

            $admin = Admin::where('email', $tokenData->email)->first();
            $user = $admin;
        } else if (Lecturer::where('email', $tokenData->email)->exists()) {

            $lecturer = Lecturer::where('email', $tokenData->email)->first();
            $user = $lecturer;
        }

        if (!$user)
            return response()->json(['email' => 'Email not found'], 400);
        //Hash and update the new password
        $user->password = bcrypt($request->input('password'));
        $user->update();


        //Delete the token
        DB::table('password_reset_tokens')->where('email', $user->email)
            ->delete();

        //Send Email Reset Success Email
        return response()->json(['message' => trans('Reset done'), 'status' => 'success'], 200);
    }

    public function updatePassword(Request $request)
    {
        $id = auth()->user()->id;
        if (Student::where('id', $id )->exists()) {
            $student = Student::where('id', $id)->first();

            $user = $student;

        } else if (Admin::where('id', $id)->exists()) {

            $admin = Admin::where('id', $id)->first();
            $user = $admin;
        } else if (Lecturer::where('id', $id)->exists()) {

            $lecturer = Lecturer::where('id', $id)->first();
            $user = $lecturer;
        }
        // $user = User::where('id', '=', auth()->user()->id);
        $user->update([
            'password' => bcrypt($request->input('password')),
            'first_login' => false
        ]);
        return response()->json(['message' => trans('Password Changed Successfully'), 'status' => 'success'], 200);
    }
}
