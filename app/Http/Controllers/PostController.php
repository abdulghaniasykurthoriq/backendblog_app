<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // get all posts
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')->get();
                })
                ->get()
        ], 200);
    }

    // get single posts
    public function show($id)
    {
        return response([
            'posts' => Post::where('id', $id)->withCount('comments', 'likes')->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'posts');


        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);
        return response([
            'message' => 'post created',
            'post' => $post
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        // $post = Post::findOrFail($id);
        if (!$post) {
            return response([
                'message' => 'post not found'
            ], 403);
        }
        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $attrs['body']
        ]);
        return response([
            'message' => 'post updated',
            'post' => $post
        ], 200);
    }


    // delete post
    public function destroy(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'message' => 'post not found'
            ], 403);
        }
        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }
        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'post deleted',
            'post' => $post
        ], 200);
    }
}
