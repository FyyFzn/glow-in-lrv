<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of comments, optionally filtered by post_id.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Comment::with('user:id,name,username,profile_pic')->latest();

        if ($request->filled('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        $comments = $query->paginate(20);

        return response()->json($comments);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
            'comment_text' => ['required', 'string', 'max:1000'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $userId = $request->user()?->id ?? $validated['user_id'] ?? auth()->id();

        if (! $userId) {
            return response()->json([
                'message' => 'User ID is required to post a comment.',
            ], 422);
        }

        $comment = Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => $userId,
            'comment_text' => $validated['comment_text'],
        ]);

        return response()->json(
            $comment->load('user:id,name,username,profile_pic'),
            201
        );
    }

    /**
     * Display the specified comment.
     */
    public function show(Comment $comment): JsonResponse
    {
        return response()->json(
            $comment->load('user:id,name,username,profile_pic')
        );
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'comment_text' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update([
            'comment_text' => $validated['comment_text'],
        ]);

        return response()->json(
            $comment->load('user:id,name,username,profile_pic')
        );
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
