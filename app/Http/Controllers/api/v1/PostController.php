<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Post;
use App\Models\Share;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user:id,name,email,bio,profile_pic')
            ->withCount('comments')
            ->withCount('likers')
            ->orderBy("created_at", "desc")
            ->paginate(10);
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid_data = $request->validate([
            "title" => "required|max:90",
            "body" => "required|min:10|max:255",
        ]);
        $valid_data["user_id"] = Auth::user()->id;
        $post = Post::create($valid_data);

        return response()->json([
            "status" => "success",
            "post" => $post
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::with(['comments' => function ($query) {
            $query->withCount('likers');
        }, 'comments.user', 'user:id,name,bio,profile_pic'])->find($id);


        $post->likes_count = $post->likersCount();
        return response()->json([
            "status" => "success",
            "post" => $post
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->id != Post::findorFail($id)->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "Unauthorized"
            ], 401);

        }

        $post = Post::findorFail($id);
        $valid_data = $request->validate([
            "title" => "required|max:90",
            "body" => "required|min:10|max:255",
        ]);
        $post->update($valid_data);
        return response()->json([
            "status" => "success",
            "message" => "Post Updated Successfully",
            "post" => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id != Post::findorFail($id)->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "Unauthorized"
            ], 401);

        }
        Post::destroy($id);
        return response()->json([
            "status" => "success",
            "message" => "Your post has been Deleted Successfully",

        ]);
    }

    public function userInteraction(Request $request, $id)
    {
        $post = Post::with('comments')->findOrFail($id);
        $user = Auth::user();
        $request->validate([
            'like' => 'required|bool'
        ]);
        if ($request->like == true && $user->hasLiked($post)) {
            return response()->json([
                "status" => "error",
                "message" => "you already liked this post !"
            ], 406);
        } else if ($request->like == false && !$user->hasLiked($post)) {
            return response()->json([
                "status" => "error",
                "message" => "you haven't liked this post !"
            ], 406);
        }
        if ($request->like == true) {
            $user->like($post);
            $post->likes_count = $post->likersCount();
            return response()->json([
                "status" => "sucess",
                "message" => "post liked !",
                "post" => $post
            ], 200);
        }


        $user->unlike($post);
        $post->likes_count = $post->likersCount();
        return response()->json([
            "status" => "sucess",
            "message" => "post unliked !",
            "post" => $post
        ], 200);
    }

    public function getLikers($id)
    {
        $post = Post::findorFail($id);
        return response()->json([
            'count' => $post->likersCount(),
            'likers' => $post->fans()->get()
        ]);

    }

    public function share ($id)
    {
        Share::create([
            'user_id'   => Auth::user()->id,
            'post_id'   => $id
        ]);

        return response()->json([
            'status'    => 'success',
            'message'   => 'post has been shared successfully'
        ]);

    }
}
