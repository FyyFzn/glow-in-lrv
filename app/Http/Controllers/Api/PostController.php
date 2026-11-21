<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of posts with author and comment count.
     */
    public function index(): JsonResponse
    {
        $posts = Post::with('user')
            ->withCount('comments')
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $posts,
        ]);
    }

    /**
     * Store a newly created post for the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $post = $request->user()->posts()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => $post->load('user'),
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): JsonResponse
    {
        $post->load(['user', 'comments.user'])->loadCount('comments');

        return response()->json([
            'status' => 'success',
            'data' => $post,
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $post->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'data' => $post->fresh('user'),
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully',
        ]);
    }
}