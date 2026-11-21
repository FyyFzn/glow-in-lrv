<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $request->user()->posts()->create($validated);

        return redirect()->route('home');
    }

    /**
     * Display the specified resource (Post Detail with Comments).
     */
    public function show(Post $post)
    {
        $post->load(['user', 'comments.user'])->loadCount('comments');

        return view('comments.detail', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        abort_unless(auth()->id() === $post->user_id, 403);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        abort_unless(auth()->id() === $post->user_id, 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)->with('status', 'Post updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        abort_unless(auth()->id() === $post->user_id, 403);

        $post->delete();

        return redirect()->route('home')->with('status', 'Post deleted');
    }
}