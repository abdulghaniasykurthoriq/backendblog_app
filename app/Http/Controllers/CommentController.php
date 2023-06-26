<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // get all coments of a post
    public function index($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'message' => 'post not found'
            ], 403);
        }
        return response([
            'comment' => $post->comments()->with('user:id,name,image')->get()
        ], 200);
    }



    // createa acommetn store
    public function store(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'message' => 'post not found'
            ], 403);
        }
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);
        return response([
            'message' => 'comment created'
        ], 200);
    }


    // update comments
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response([
                'message' => 'comment not found'
            ], 403);
        }
        if ($comment->user_id != auth()->user()->id) {
            return response([
                'message' => 'permission denied'
            ]);
        }

        // validate field
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment']
        ]);
        return response([
            'message' => 'comment created'
        ]);
    }




    public function destroy($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response([
                'message' => 'comment not found'
            ], 403);
        }
        if ($comment->user_id != auth()->user()->id) {
            return response([
                'message' => 'permission denied'
            ]);
        }
        $comment->delete();
        return response([
            'message' => 'comment delete'
        ]);
    }
}
