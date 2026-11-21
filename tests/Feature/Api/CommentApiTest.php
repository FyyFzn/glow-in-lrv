<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

test('can list comments for a post', function () {
    $post = Post::factory()->create();
    Comment::factory()->count(3)->create(['post_id' => $post->id]);

    $response = $this->getJson("/api/comments?post_id={$post->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'post_id', 'user_id', 'comment_text', 'user'],
            ],
        ]);
});

test('can create a comment via api', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/comments', [
        'post_id' => $post->id,
        'comment_text' => 'Test komentar API.',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment_text' => 'Test komentar API.',
        ]);

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'comment_text' => 'Test komentar API.',
    ]);
});

test('can show a comment', function () {
    $comment = Comment::factory()->create();

    $response = $this->getJson("/api/comments/{$comment->id}");

    $response->assertStatus(200)
        ->assertJsonFragment([
            'id' => $comment->id,
            'comment_text' => $comment->comment_text,
        ]);
});

test('can update a comment', function () {
    $comment = Comment::factory()->create([
        'comment_text' => 'Original text',
    ]);

    $response = $this->putJson("/api/comments/{$comment->id}", [
        'comment_text' => 'Updated text',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'id' => $comment->id,
            'comment_text' => 'Updated text',
        ]);

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'comment_text' => 'Updated text',
    ]);
});

test('can delete a comment', function () {
    $comment = Comment::factory()->create();

    $response = $this->deleteJson("/api/comments/{$comment->id}");

    $response->assertStatus(200)
        ->assertJsonFragment([
            'message' => 'Comment deleted successfully.',
        ]);

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});
