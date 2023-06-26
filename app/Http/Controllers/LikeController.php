<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    // like or unlike
    public function likeOrUnlike($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'message' => 'post not found'
            ], 403);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id
            ]);
            return response([
                'message' => 'like created'
            ], 200);
        }

        //else
        $like->delete();
        return response([
            'message' => 'like deleted'
        ], 200);
    }
}
