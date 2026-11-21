<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/CSS/detail.css') }}">
        <style>
            .comment-edit-box {
                margin-top: 8px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .comment-edit-textarea {
                width: 100%;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 8px;
                font-size: 14px;
                resize: vertical;
                min-height: 60px;
                outline: none;
                font-family: inherit;
            }
            .comment-action-link {
                background: none;
                border: none;
                cursor: pointer;
                font-size: 12px;
                font-weight: 600;
                padding: 0 4px;
                color: var(--text-tertiary, #999);
            }
            .comment-action-link.edit:hover { color: #3498db; }
            .comment-action-link.delete:hover { color: #e74c3c; }
            .comment-reply-card {
                background-color: var(--card-bg, #fff);
                border-radius: 20px;
                padding: 20px 24px;
                margin: 0 40px 20px 40px;
            }
            .comment-reply-card textarea {
                width: 100%;
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                padding: 12px;
                font-size: 14px;
                resize: none;
                min-height: 70px;
                outline: none;
                margin-bottom: 12px;
                font-family: inherit;
            }
            .comment-reply-card textarea:focus {
                border-color: var(--accent-orange, #F58A42);
            }
            .reply-btn {
                background: var(--accent-orange, #F58A42);
                color: #fff;
                border: none;
                padding: 8px 20px;
                border-radius: 20px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: opacity 0.2s;
            }
            .reply-btn:hover {
                opacity: 0.9;
            }
        </style>
    @endpush

    <header class="top-header">
        <a href="{{ route('home') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Detail</h1>
    </header>

    {{-- Post Card (Exact Match with Reference detail.php) --}}
    <div class="post-card">
        <div class="post-header">
            <img src="{{ $post->user?->profile_pic ?: asset('assets/IMG/jaki.jpg') }}" class="avatar" alt="Avatar">
            <div class="post-user-info">
                <div class="name">{{ $post->user?->username ?: 'user' }}</div>
                <div class="handle">@{{ $post->user?->username ?: 'user' }}</div>
            </div>
        </div>

        <p class="post-body">{!! nl2br(e($post->content)) !!}</p>

        <div class="post-meta">
            <span class="time">{{ $post->created_at ? $post->created_at->format('d M Y H:i') : now()->format('d M Y H:i') }}</span>
            
            <div class="interactions">
                <div class="interaction-item">
                    <i class="fa-regular fa-heart"></i>
                    <span>0</span>
                </div>
                <div class="interaction-item">
                    <i class="fa-regular fa-comment"></i>
                    <span id="comments-count">0</span>
                </div>
                <div class="interaction-item">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span>0</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Inline Reply Box --}}
    <div class="comment-reply-card">
        <form id="comment-form">
            <textarea id="comment-text-input" placeholder="Post your reply..." required></textarea>
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" id="submit-comment-btn" class="reply-btn">Reply</button>
            </div>
        </form>
    </div>

    {{-- Comments Section (Exact Match with Reference detail.php) --}}
    <div class="comments-section">
        <h3>Comments (<span id="comments-total">0</span>)</h3>
        
        <div id="comments-list">
            <div style="text-align: center; color: var(--text-tertiary, #999); padding: 20px 0;">
                Loading...
            </div>
        </div>
    </div>

    {{-- Native API Connection Script --}}
    <script>
        const POST_ID = {{ $post->id }};
        const CURRENT_USER_ID = {{ auth()->id() }};
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const elements = {
            list: document.getElementById('comments-list'),
            form: document.getElementById('comment-form'),
            input: document.getElementById('comment-text-input'),
            countTop: document.getElementById('comments-count'),
            countTotal: document.getElementById('comments-total'),
            submitBtn: document.getElementById('submit-comment-btn'),
        };

        // 1. READ: GET /api/comments?post_id={POST_ID}
        async function loadComments() {
            try {
                const response = await fetch(`/api/comments?post_id=${POST_ID}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                const comments = data.data || data;

                renderComments(comments);
            } catch (err) {
                elements.list.innerHTML = `<div style="text-align:center;color:#999;padding:20px;">Could not load comments.</div>`;
            }
        }

        function renderComments(comments) {
            elements.countTop.textContent = comments.length;
            elements.countTotal.textContent = comments.length;

            if (comments.length === 0) {
                elements.list.innerHTML = `<div style="color: var(--text-tertiary, #999); text-align: center; padding: 24px 0;">No comments yet.</div>`;
                return;
            }

            elements.list.innerHTML = comments.map(comment => {
                const isAuthor = comment.user_id === CURRENT_USER_ID;
                const avatar = comment.user && comment.user.profile_pic ? comment.user.profile_pic : '{{ asset("assets/IMG/jaki.jpg") }}';
                const name = comment.user ? (comment.user.name || comment.user.username) : 'User';
                const username = comment.user ? comment.user.username : 'user';
                const time = comment.created_at ? new Date(comment.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';

                return `
                    <div class="comment" id="comment-${comment.id}">
                        <img src="${avatar}" class="comment-avatar" alt="Avatar">
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-name">${escapeHtml(name)}</span>
                                <span class="comment-handle">@${escapeHtml(username)}</span>
                                <span class="dot">•</span>
                                <span class="comment-time">${escapeHtml(time)}</span>
                            </div>
                            <div id="comment-text-${comment.id}" class="comment-text">
                                ${escapeHtml(comment.comment_text).replace(/\\n/g, '<br>')}
                            </div>
                            ${isAuthor ? `
                                <div class="comment-actions" style="margin-top: 6px;">
                                    <button class="comment-action-link edit" onclick="startEdit(${comment.id})">Edit</button>
                                    <span style="color: #ddd;">•</span>
                                    <button class="comment-action-link delete" onclick="deleteComment(${comment.id})">Delete</button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        // 2. CREATE: POST /api/comments
        elements.form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const text = elements.input.value.trim();
            if (!text) return;

            elements.submitBtn.disabled = true;

            try {
                const response = await fetch('/api/comments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({
                        post_id: POST_ID,
                        user_id: CURRENT_USER_ID,
                        comment_text: text,
                    }),
                });

                if (response.ok) {
                    elements.input.value = '';
                    await loadComments();
                }
            } catch (err) {
                console.error(err);
            } finally {
                elements.submitBtn.disabled = false;
            }
        });

        // 3. DELETE: DELETE /api/comments/{id}
        async function deleteComment(id) {
            if (!confirm('Delete this comment?')) return;

            try {
                const response = await fetch(`/api/comments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                });

                if (response.ok) {
                    const el = document.getElementById(`comment-${id}`);
                    if (el) el.remove();
                    await loadComments();
                }
            } catch (err) {
                console.error(err);
            }
        }

        // 4. UPDATE: PUT /api/comments/{id}
        function startEdit(id) {
            const textEl = document.getElementById(`comment-text-${id}`);
            const rawText = textEl.textContent.trim();

            textEl.innerHTML = `
                <div class="comment-edit-box">
                    <textarea id="edit-textarea-${id}" class="comment-edit-textarea">${escapeHtml(rawText)}</textarea>
                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                        <button style="background: none; border: 1px solid #ccc; padding: 4px 12px; border-radius: 12px; font-size: 12px; cursor: pointer;" onclick="loadComments()">Cancel</button>
                        <button class="reply-btn" style="padding: 4px 14px; font-size: 12px;" onclick="saveEdit(${id})">Save</button>
                    </div>
                </div>
            `;
        }

        async function saveEdit(id) {
            const input = document.getElementById(`edit-textarea-${id}`);
            const updated = input.value.trim();
            if (!updated) return;

            try {
                const response = await fetch(`/api/comments/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ comment_text: updated }),
                });

                if (response.ok) {
                    await loadComments();
                }
            } catch (err) {
                console.error(err);
            }
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        document.addEventListener('DOMContentLoaded', loadComments);
    </script>
</x-app-layout>
