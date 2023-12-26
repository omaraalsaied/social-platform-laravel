<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, $post_id)
    {
        $post = Post::with('comments')->findorFail($post_id);
        $request->validate([
            'body' => 'required|max:255'
        ]);

        $comment = new Comment;
        $comment->post_id = $post['id'];
        $comment->user_id = Auth::user()->id;
        $comment->body = $request->body;
        $comment->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Your comment has been added successfully',
            'post'      =>$post
        ], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $comment = Comment::with(['user:id,name,email,bio,profile_pic'])->withCount('likers')->findOrFail($id);
        return response()->json([
            "status" => "success",
            "comment" => $comment
        ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrfail($id);
        if($comment->user_id != Auth::user()->id)
        {
            return response()->json([
                'status'=> 'error',
                'message'=> 'Unauthorized'
            ],401);
        }

        $valid_data = $request->validate([
            "body" => "required|max:255"
        ]);

        $comment->Update($valid_data);

        $post = Post::with(['user:id,name,email,bio,profile_pic',
         'comments.user:id,name,bio'])
         ->findOrFail($comment->post_id);

        return response()->json([
            "status"=> "success",
            "message"=> "Comment Has been updated successfully",
            "post" => $post
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrfail($id);
        if($comment->user_id != Auth::user()->id)
        {
            return response()->json([
                'status'=> 'error',
                'message'=> 'Unauthorized'
            ],401);
        }
        $comment->delete();
        return response()->json([
            "status"=> "success",
            "message" => "Your comment has been Deleted Successfully",

        ]);
    }

    public function userInteraction(Request $request, $id)
    {
        $comment = comment::findOrFail($id);
        $user = Auth::user();
        $request->validate([
            'like' => 'required|bool'
        ]);
        if($request->like == true && $user->hasLiked($comment))
        {
            return response()->json([
                "status" => "error",
                "message" => "you already liked this comment !"
            ], 406);
        } else if ($request->like == false && !$user->hasLiked($comment))
        {
            return response()->json([
                "status" => "error",
                "message" => "you haven't liked this comment !"
            ], 406);
        }
        if($request->like == true) {
            $user->like($comment);
            $comment->likes_count = $comment->likersCount();
            return response()->json([
                "status" => "sucess",
                "message" => "comment liked !",
                "comment" => $comment
            ], 200);
        }


        $user->unlike($comment);
        $comment->likes_count = $comment->likersCount();
        return response()->json([
                "status" => "sucess",
                "message" => "comment unliked !",
                "comment" => $comment
            ], 200);
    }

    public function getLikers($id)
    {
        $comment = Comment::findorFail($id);
        return response()->json([
            'count' => $comment->likersCount(),
            'likers' => $comment->fans()->get()
        ]);

    }
}
