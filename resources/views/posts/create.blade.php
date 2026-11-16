<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/CSS/post.css') }}">
    @endpush

    <header class="top-header">
        <label for="menu-toggle" class="menu-toggle-btn" aria-label="Buka Menu">
            <i class="fa-solid fa-bars"></i>
        </label>
        <h1>Create Post</h1>
    </header>

    <div class="container">
        <form action="{{ route('posts.store') }}" method="POST" class="post-card">
            @csrf

            <div class="post-header">
                <img src="{{ auth()->user()->profile_pic ?: asset('assets/IMG/jaki.jpg') }}" class="avatar" alt="Avatar">
                <div>
                    <div class="name">{{ auth()->user()->name ?: auth()->user()->username }}</div>
                    <div class="handle">{{ '@' . auth()->user()->username }}</div>
                </div>
            </div>

            <textarea name="content" class="post-textarea" placeholder="What's on your mind?" required>{{ old('content') }}</textarea>
            @error('content')
                <p class="field-error">{{ $message }}</p>
            @enderror

            <div class="upload-box">
                <i class="fa-regular fa-image"></i>
                <span>Upload image</span>
            </div>

            <div class="post-actions">
                <a href="{{ route('home') }}" class="btn cancel">Cancel</a>
                <button type="submit" class="btn submit">Post</button>
            </div>
        </form>
    </div>
</x-app-layout>
