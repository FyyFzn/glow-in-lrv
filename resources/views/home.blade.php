<x-app-layout>
    <main class="main-content">
        <header class="top-header">
            <label for="menu-toggle" class="menu-toggle-btn" aria-label="Buka Menu">
                <i class="fa-solid fa-bars"></i>
            </label>
            <div class="main-nav-tabs">
                <a href="#" class="tab active">For you</a>
                <a href="#" class="tab">Following</a>
            </div>
        </header>

        <div class="container">
            @forelse ($posts as $post)
            <article class="tweet-card" style="cursor: pointer;">
                <div onclick="window.location.href='{{ route('posts.show', $post->id) }}';" class="click-post" style="display:block;">
                    <div class="post-header">
                        <img src="{{ asset($post->user->profile_pic ?? 'assets/IMG/default-avatar.png') }}" class="avatar" alt="Avatar">
                        <div class="post-user-info">
                            <div class="name">{{ $post->user->username }}</div>
                            <div class="handle">{{ '@' . $post->user->username }} • {{ $post->created_at->diffForHumans() }}</div>
                        </div>
                        
                        @if (auth()->id() === $post->user_id)
                        <div class="post-dropdown" onclick="event.stopPropagation();">
                            <button class="post-dropdown-btn" onclick="this.nextElementSibling.classList.toggle('show');">
                                <i class="fa-solid fa-ellipsis"></i>
                            </button>
                            <div class="post-dropdown-content">
                                <a href="{{ route('posts.edit', $post->id) }}">
                                    <i class="fa-solid fa-pen-to-square" style="color: blue;"></i> Edit
                                </a>
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="width: 100%; text-align: left;">
                                        <i class="fa-solid fa-trash" style="color: red;"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <p class="post-body" style="margin-top: 10px;">{!! nl2br(e($post->content)) !!}</p>
                    
                    <div class="tweet-actions" style="margin-top: 15px;">
                        <div class="item"><i class="fa-regular fa-heart"></i><span>0</span></div>
                        <div class="item" onclick="event.stopPropagation();">
                            <i class="fa-regular fa-comment"></i>
                            <span>{{ $post->comments_count ?? 0 }}</span>
                        </div>
                        <div class="item" onclick="event.stopPropagation();"><i class="fa-solid fa-share-nodes"></i><span>0</span></div>
                    </div>
                </div>
            </article>
            @empty
            <div class="empty-state">
                <p>Belum ada curhatan. Jadilah yang pertama!</p>
            </div>
            @endforelse
        </div>
    </main>
</x-app-layout>