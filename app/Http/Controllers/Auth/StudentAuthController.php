<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewStudent;
use App\Mail\PasswordReset;
use App\Models\Student;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Mail;
use Symfony\Component\Console\Input\Input;
use Validator;

class StudentAuthController extends Controller
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

    public function Register(Request $request)
    {

        $validated = FacadesValidator::make($request->all(), [
            'email' => 'required|email|unique:students',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|string',
            'phone' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'education' => 'required|string',
            'baptized' => 'sometimes|string',
            'attended_som_before' => 'sometimes|string',
            'where_attended' => 'sometimes|string',
            'participation_mode' => 'sometimes|string',
            'ln_member' => 'sometimes|string',
            'ministry' => 'sometimes|string',
            'ministry_role' => 'sometimes|string',
            'salvation_experience' => 'sometimes|string',
            'expectations' => 'sometimes|string',
        ]);
        if ($validated->fails()) {
            if ($validated->errors()->first() == "The email has already been taken.") {
                return response()->json([
                    "status" => "error",
                    "message" => "Email already exists",
                    "student" => Student::where('email', $request->email)->first()
                ], 400);
            }
            return response()->json([
                "status" => "error",
                "message" => $validated->errors()->first()
            ], 400);
        }


        $student = Student::create($request->all());

        $token = $student->createToken('user');

        event(new Registered($student));
        if ($student) {
            return response()->json([
                "status" => "success",
                "message" => "Registration Successful",
                "student" => $student,
                "token" => $token
            ], 201);
        }
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
