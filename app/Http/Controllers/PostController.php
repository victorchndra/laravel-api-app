<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::with('user')->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        // store method is looking for an authenticated user then
        // it will be able to create a post
        $post = $request->user()->posts()->create($fields);

        return [
            'post' => $post,
            'user' => $post->user
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return [
            'post' => $post,
            'user' => $post->user
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        // $user is automatically passed in, so we just need to pass down $post
        // so the laravel will automatically check for the user ID
        Gate::authorize('modify', $post);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post->update($fields);

        return [
            'post' => $post,
            'user' => $post->user
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);

        $post->delete($post);

        return ['message' => 'The post was deleted!'];
    }
}
