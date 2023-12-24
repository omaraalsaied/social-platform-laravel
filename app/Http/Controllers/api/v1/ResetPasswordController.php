<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request) {
        $credentials = $request->validate([
            "email" => "required | email",
        ]);

        $user = User::where('email', $credentials["email"])->first();
        if(is_null($user)){
            return response()->json([
                "status" => "error",
                "message"=> "A user with this email can't be found"
            ], 404);
        }

        Password::sendResetLink($credentials);
        return response()->json([
            "status" => "success",
            "message" => "Password reset Email has been sent successfully !"
        ]);

    }

    public function confirmReset(Request $request)
     {
        $credentials = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["msg" => "Invalid token provided"], 400);
        }

        return response()->json([
            "status" => "success",
            "message" => "Password has been successfully changed"
        ]);
    }




}
