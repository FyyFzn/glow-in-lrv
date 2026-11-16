<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Display the homepage feed.
     */
    public function index()
    {
        $posts = Post::with('user')
            ->withCount('comments')
            ->latest()
            ->get();

        return view('home', compact('posts'));
    }
}
