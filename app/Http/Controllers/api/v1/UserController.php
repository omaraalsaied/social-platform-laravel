<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function view($id)
    {
        $user = User::find($id);
        return response()->json([
            "status" => "success",
            "user" =>   $user->makeHidden(['provider_name', 'provider_id'])
        ], 200);
    }

    public function edit ($id) {
        if(Auth::user()->id !== $id)
        {
            return response()->json([
                'message'=> 'Unauthorized'
            ], 401);
        }
        $user = User::findorfail($id);
        return response()->json([
            "message" => "success",
            "user"  => $user
        ], 200);
    }


    public function update(Request $request,  $id)
    {
        if(Auth::user()->id !==(int) $id )
        {
            return response()->json([
                'message'=> 'Unauthorized'
            ], 401);
        }
        $valid_data = $request->validate([
            'name'                  => 'string|max:255',
            'email'                 => 'string|email|max:255',
            'password'              => 'string|min:8',
            'phone'                 => 'string|min:11|max:13',
            'profile_pic'           => 'string|max:255',
            'bio'                   => 'string|max:255',
        ]);
        $user = User::findorfail($id);
        $user->update($valid_data);
        return response()->json([
            "message" => "success",
            "user" => $user->makeHidden(["provider_name", "provider_id"])
        ]);

    }
}
