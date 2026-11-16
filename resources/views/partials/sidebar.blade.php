@php
    $user = auth()->user();
    $avatar = $user?->profile_pic ?: asset('assets/IMG/jaki.jpg');
    $name = $user?->name ?: $user?->username ?: 'User';
    $username = $user?->username ?: 'user';
@endphp

<aside class="sidebar-left">
    <div class="logo">
        <i class="fa-solid fa-message logo-icon"></i>
        <span>Glow-in</span>
    </div>
    <label for="menu-toggle" class="close-btn" aria-label="Tutup Menu">
        <i class="fas fa-times"></i>
    </label>

    <nav class="nav-menu">
        <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('posts.create') }}" class="nav-item {{ request()->routeIs('posts.create') ? 'active' : '' }}">
            <i class="fa-solid fa-plus"></i>
            <span>Create Post</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span>Explore</span>
        </a>
        <a href="#" class="nav-item">
            <div class="icon-wrapper">
                <i class="fa-solid fa-bell"></i>
                <span class="badge">20+</span>
            </div>
            <span>Notifications</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fa-regular fa-envelope"></i>
            <span>Messages</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-chart-simple"></i>
            <span>Leaderboard</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fa-regular fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <a href="{{ route('posts.create') }}" class="post-btn">Post</a>

    <div class="user-profile-mini" style="position: relative;">
        <img src="{{ $avatar }}" alt="Profile" class="avatar-mini">
        <div class="user-info-mini">
            <div class="name">{{ $name }}</div>
            <div class="handle">{{ '@' . $username }}</div>
        </div>
        <div class="post-dropdown" onclick="event.stopPropagation();">
            <button type="button" class="post-dropdown-btn" onclick="this.nextElementSibling.classList.toggle('show');" style="padding: 0; margin-left: 5px; display: flex; align-items: center;">
                <i class="fa-solid fa-ellipsis menu-dots"></i>
            </button>
            <div class="post-dropdown-content" style="bottom: 100%; top: auto; right: 0; margin-bottom: 10px; min-width: 120px;">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" style="color: #ff4757;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
