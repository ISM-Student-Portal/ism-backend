<?php

namespace App\Http\Controllers;

use App\Mail\NewUser;
use App\Mail\PasswordReset;
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
        $validated['is_active'] = 1;
        if (Auth::attempt($validated)) {
            $token = auth()->user()->createToken('user');
            $user = User::where('id', auth()->user()->id)->with(['attendances', 'profile'])->first();
            return response()->json([
                "status" => "success",
                'user' => $user,
                "token" => $token
            ], 201);
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
        $user = User::where('email', '=', $email)->first();
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
        $user = User::where('email', $tokenData->email)->first();
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
        $user = User::where('id', '=', auth()->user()->id);
        $user->update([
            'password' => bcrypt($request->input('password')),
            'first_login' => false
        ]);
        return response()->json(['message' => trans('Password Changed Successfully'), 'status' => 'success'], 200);
    }
}
