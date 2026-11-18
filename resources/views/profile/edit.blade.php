<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/CSS/post.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/CSS/profile.css') }}">
    @endpush

    <header class="top-header">
        <label for="menu-toggle" class="menu-toggle-btn" aria-label="Buka Menu">
            <i class="fa-solid fa-bars"></i>
        </label>
        <h1>Edit Profile</h1>
    </header>

    <div class="container">
        <div class="post-card profile-edit-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="post-card profile-edit-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="post-card profile-edit-card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
