@php
    $profile = $user->profile;
    $avatar = $profile?->profile_pic ?: asset('assets/IMG/jaki.jpg');
    $cover = $profile?->header_pic ?: asset('assets/IMG/bareng.jpg');
    $displayName = $user->name ?: $user->username;
@endphp

<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/CSS/profile.css') }}">
    @endpush

    <header class="top-header">
        <label for="menu-toggle" class="menu-toggle-btn" aria-label="Buka Menu">
            <i class="fa-solid fa-bars"></i>
        </label>
        <h1>Profile</h1>
    </header>

    <div class="container profile-page">
        <section class="profile-card">
            <div class="profile-cover" style="background-image: url('{{ $cover }}');"></div>

            <div class="profile-inner">
                <div class="profile-header">
                    <img src="{{ $avatar }}" alt="{{ $displayName }}" class="avatar-large">

                    <div class="profile-info">
                        <div class="profile-name-row">
                            <h1 class="profile-name">{{ $displayName }}</h1>
                        </div>
                        <div class="profile-handle">{{ '@' . $user->username }}</div>
                    </div>

                    <div class="profile-actions">
                        @if ($isOwnProfile)
                            <a href="{{ route('profile.edit') }}" class="btn submit edit-profile-btn">Edit Profile</a>
                        @elseif ($isFollowing)
                            <form action="{{ route('profile.unfollow', $user->username) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn follow-btn">Following</button>
                            </form>
                        @else
                            <form action="{{ route('profile.follow', $user->username) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn submit follow-btn">Follow</button>
                            </form>
                        @endif
                    </div>
                </div>

                @if ($profile?->bio)
                    <p class="profile-bio">{{ $profile->bio }}</p>
                @endif

                <div class="profile-meta">
                    @if ($profile?->location)
                        <span><i class="fa-solid fa-location-dot"></i> {{ $profile->location }}</span>
                    @endif
                    <span><i class="fa-regular fa-calendar"></i> Joined {{ $user->created_at->format('F Y') }}</span>
                </div>

                <div class="profile-stats">
                    <div class="stat">
                        <strong>{{ number_format($user->posts_count) }}</strong>
                        <span>{{ Str::plural('Post', $user->posts_count) }}</span>
                    </div>
                    <div class="stat">
                        <strong>{{ number_format($user->followers_count) }}</strong>
                        <span>{{ Str::plural('Follower', $user->followers_count) }}</span>
                    </div>
                    <div class="stat">
                        <strong>{{ number_format($user->following_count) }}</strong>
                        <span>Following</span>
                    </div>
                </div>
            </div>

            <nav class="profile-tabs">
                <a href="{{ route('profile.show', ['user' => $user->username, 'tab' => 'posts']) }}"
                   class="profile-tab {{ $tab === 'posts' ? 'active' : '' }}">Posts</a>
                <a href="{{ route('profile.show', ['user' => $user->username, 'tab' => 'replies']) }}"
                   class="profile-tab {{ $tab === 'replies' ? 'active' : '' }}">Replies</a>
                <a href="{{ route('profile.show', ['user' => $user->username, 'tab' => 'media']) }}"
                   class="profile-tab {{ $tab === 'media' ? 'active' : '' }}">Media</a>
                <a href="{{ route('profile.show', ['user' => $user->username, 'tab' => 'likes']) }}"
                   class="profile-tab {{ $tab === 'likes' ? 'active' : '' }}">Likes</a>
            </nav>
        </section>

        @if ($tab === 'posts')
            @forelse ($posts as $post)
                <article class="tweet-card">
                    <div class="post-header">
                        <img src="{{ $post->user->profile_pic ?: asset('assets/IMG/jaki.jpg') }}" class="avatar" alt="Avatar">
                        <div class="post-user-info">
                            <div class="name">{{ $post->user->name ?: $post->user->username }}</div>
                            <div class="handle">{{ '@' . $post->user->username }} • {{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <p class="post-body" style="margin-top: 10px;">{!! nl2br(e($post->content)) !!}</p>

                    <div class="tweet-actions">
                        <div class="item"><i class="fa-regular fa-heart"></i><span>0</span></div>
                        <div class="item"><i class="fa-regular fa-comment"></i><span>{{ $post->comments_count ?? 0 }}</span></div>
                        <div class="item"><i class="fa-solid fa-share-nodes"></i><span>0</span></div>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <p>{{ $isOwnProfile ? "You haven't posted anything yet." : 'No posts yet.' }}</p>
                    @if ($isOwnProfile)
                        <a href="{{ route('posts.create') }}" class="btn submit" style="margin-top: 16px;">Create Post</a>
                    @endif
                </div>
            @endforelse
        @elseif ($tab === 'replies')
            @forelse ($replies as $reply)
                <article class="tweet-card reply-card">
                    <div class="reply-context">
                        Replying to
                        <span>{{ $reply->post?->user ? '@' . $reply->post->user->username : 'a post' }}</span>
                        • {{ $reply->created_at->diffForHumans() }}
                    </div>
                    <p class="post-body" style="margin-top: 10px;">{!! nl2br(e($reply->comment_text)) !!}</p>
                    @if ($reply->post)
                        <p class="reply-post-preview">{{ Str::limit($reply->post->content, 160) }}</p>
                    @endif
                </article>
            @empty
                <div class="empty-state">
                    <p>{{ $isOwnProfile ? "You haven't replied to any posts yet." : 'No replies yet.' }}</p>
                </div>
            @endforelse
        @elseif ($tab === 'media')
            <div class="empty-state">
                <p>No media posts yet.</p>
            </div>
        @else
            <div class="empty-state">
                <p>No liked posts to show yet.</p>
            </div>
        @endif
    </div>
</x-app-layout>
