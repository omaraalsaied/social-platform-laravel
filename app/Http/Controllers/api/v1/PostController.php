<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Post;
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
        $posts = Post::with('user:id,name,email,bio,profile_pic')->withCount('comments')->orderBy("created_at","desc")->get();
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
        $post = Post::with(['user:id,name,email,bio,profile_pic', 'comments.user:id,name,bio'])->findOrFail($id);
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
        if(Auth::user()->id != Post::findorFail($id)->user_id)
        {
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
                "status"=> "success",
                "message" => "Post Updated Successfully",
                "post" => $post
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(Auth::user()->id != Post::findorFail($id)->user_id)
        {
            return response()->json([
                "status" => "error",
                "message" => "Unauthorized"
            ], 401);

        }
        Post::destroy($id);
        return response()->json([
            "status"=> "success",
            "message" => "Your post has been Deleted Successfully",

        ]);
    }
}
