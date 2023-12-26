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
        $user = User::with('sharedPosts.post')->find($id);
        return response()->json([
            "status" => "success",
            "user" =>   $user
        ], 200);
    }

    public function edit ($id) {

    }


    public function update(Request $request)
    {
        $user = Auth::user();
        $valid_data = $request->validate([
            'name'                  => 'string|max:255',
            'email'                 => 'string|email|max:255',
            'password'              => 'string|min:8',
            'phone'                 => 'string|min:11|max:13',
            'profile_pic'           => 'string|max:255',
            'bio'                   => 'string|max:255',
        ]);
        $user->update($valid_data);
        return response()->json([
            "message" => "success",
            "user" => $user->makeHidden(["provider_name", "provider_id"])
        ]);

    }


    public function sendFriendRequest ($id)
    {
        $recipient = User::findorfail($id);
        $user = Auth::user();


        if($recipient->id == $user->id)
        {
            return response()->json([
                "status" => "error",
                "message" => "an error has been occured"
            ], 406);
        } else if ($user->hasBlocked($recipient) || $user->isBlockedBy($recipient))
        {
            return response()->json([
                "status" => "error",
                "message" => "Couldn't send the request because either you're blocking the user or he's blocking you"
            ], 400);

        } else if ($user->hasSentFriendRequestTo($recipient))
        {
            return response()->json([
                "status" => "error",
                "message" => "Friend request already sent"
            ], 400);
        }
        $user->befriend($recipient);
            return response()->json([
                "status" => "success",
                "message" => "Friend Request has been send successfully"
            ], 200);

    }

    public function getFriendRequests ()
    {
        $user = Auth::user();
        return response()->json([
            "status"=> "success",
            "pending requests" => $user->getPendingFriendships()
        ]);

    }

    public function RespondToFriendRequest (Request $request, $id)
    {
        $sender = User::findorfail($id);
        $user = Auth::user();
        $request->validate([
            'response' => 'required|boolean'
        ]);
        if($request->response == true) {
            $user->acceptFriendRequest($sender);
            return response()->json([
                'status'=> 'success',
                'message' => 'friend request has been accepted'
            ]);
        }

        $user->denyFriendRequest($sender);
        return response()->json([
            'status'=> 'success',
            'message' => 'friend request has been Declined'
        ]);

    }


    public function listFriends() {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'friends' => $user->getAllFriendships()
        ]);
    }

    public function manageFriend(Request $request , $id)
    {
        $friend = User::findorFail($id);
        $user = Auth::user();


        $valid_data = $request->validate([
            'action' => 'required|in:unfriend,block,unblock'
        ]);

        if($valid_data['action'] == 'unfriend')
        {
            $user->unfriend($friend);
            return response()->json([
                'status' => 'success',
                'friends' => $user->getAllFriendships()
            ]);

        } else if($valid_data['action'] == 'block')
        {
            $user->blockFriend($friend);
            return response()->json([
                'status' => 'success',
                'friends' => $user->getAllFriendships()
            ]);
        }
        else if($valid_data['action'] == 'unblock')
        {
            $user->unblockFriend($friend);
            return response()->json([
                'status' => 'success',
                'friends' => $user->getAllFriendships()
            ]);
        }
    }

    public function search (Request $request)
    {
        $request->validate([
            'search_param' => 'required'
        ]);

        $users = User::where('name', 'like', '%' . $request->search_param . '%')
            ->orwhere('phone', 'like', '%' . $request->search_param . '%')
            ->orwhere('bio', 'like', '%' . $request->search_param . '%')
            ->paginate(10);

        return response()->json([
            "status" => 'success',
            "data"   => $users
        ], 200);
    }

    public function profile ()
    {
        $user = Auth::user();
        return response()->json([
            "status" => "success",
            "user" =>   $user,
            "feed"  => $user->sharedPosts,
            "friends"   => $user->getFriends()
        ], 200);
    }

}
