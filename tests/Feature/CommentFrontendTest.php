<?php

use App\Models\User;

test('authenticated user can access detail page with comments thread', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('detail'));

    $response->assertStatus(200)
        ->assertSee('Detail')
        ->assertSee('Post your reply...')
        ->assertSee('Comments');
});

test('unauthenticated guest is redirected to login from detail page', function () {
    $response = $this->get(route('detail'));

    $response->assertRedirect('/login');
});

test("posts show page displays post detail and comment thread", function () {
    $user = \App\Models\User::factory()->create();
    $post = \App\Models\Post::factory()->create(["user_id" => $user->id]);

    $response = $this->actingAs($user)->get(route("posts.show", $post->id));

    $response->assertStatus(200)
        ->assertSee("Detail")
        ->assertSee("Post your reply...")
        ->assertSee("Comments");
});
