<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat API - Glow-In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali ke Beranda</a>
            <h5 class="mb-0 fw-bold">Chat Antar User (REST API)</h5>
        </div>
        <span class="small">Login sebagai: <strong>{{ Auth::user()->name }}</strong></span>
    </div>

    <div id="alert-box"></div>

    <div class="row g-0 shadow-sm bg-white rounded overflow-hidden border" style="height: 75vh;">
        <!-- Sidebar Daftar User -->
        <div class="col-4 border-end overflow-auto h-100">
            <div class="p-3 bg-primary text-white fw-bold">Daftar Pengguna</div>
            <div class="list-group list-group-flush" id="user-list">
                <div class="p-3 text-muted small">Memuat pengguna...</div>
            </div>
        </div>

        <!-- Area Chat -->
        <div class="col-8 d-flex flex-column h-100">
            <div class="p-3 border-bottom bg-white fw-bold" id="chat-header">
                Pilih pengguna untuk mulai mengobrol
            </div>

            <!-- Daftar Pesan (GET /api/chat/{userId}/messages) -->
            <div class="flex-grow-1 p-3 overflow-auto bg-light" id="chat-box">
                <p class="text-center text-muted my-auto">Pilih pengguna di sebelah kiri.</p>
            </div>

            <!-- Form Kirim Pesan (POST /api/chat/{userId}/messages) -->
            <div class="p-3 bg-white border-top d-none" id="chat-form-container">
                <form id="send-form" class="d-flex gap-2">
                    <input type="text" id="message-input" class="form-control" placeholder="Tulis pesan..." required autocomplete="off">
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pesan (PUT /api/chat/messages/{id}) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="edit-form" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-message-id">
                <textarea id="edit-message-input" class="form-control" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const currentUserId = {{ Auth::id() }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let activeUserId = null;
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));

    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    };

    function showAlert(msg) {
        document.getElementById('alert-box').innerHTML = `
            <div class="alert alert-success alert-dismissible fade show py-2">
                ${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
    }

    // 1. READ USERS: GET /api/chat/users
    async function loadUsers() {
        const res = await fetch('/api/chat/users', { headers });
        const json = await res.json();
        const list = document.getElementById('user-list');
        list.innerHTML = '';

        json.data.forEach(user => {
            const btn = document.createElement('button');
            btn.className = 'list-group-item list-group-item-action text-start';
            btn.textContent = user.name;
            btn.onclick = () => selectUser(user.id, user.name, btn);
            list.appendChild(btn);
        });
    }

    function selectUser(userId, userName, element) {
        activeUserId = userId;
        document.querySelectorAll('#user-list button').forEach(b => b.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('chat-header').textContent = `Chat dengan ${userName}`;
        document.getElementById('chat-form-container').classList.remove('d-none');
        loadMessages();
    }

    // 2. READ MESSAGES: GET /api/chat/{userId}/messages
    async function loadMessages() {
        if (!activeUserId) return;
        const res = await fetch(`/api/chat/${activeUserId}/messages`, { headers });
        const json = await res.json();
        const box = document.getElementById('chat-box');
        box.innerHTML = '';

        if (json.data.length === 0) {
            box.innerHTML = '<p class="text-center text-muted my-auto">Belum ada pesan. Mulai obrolan sekarang!</p>';
            return;
        }

        json.data.forEach(msg => {
            const isMine = msg.sender_id === currentUserId;
            const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const editedLabel = msg.is_edited ? ' <em>(diedit)</em>' : '';

            const actions = isMine ? `
                <div class="mt-1">
                    <button class="btn btn-link btn-sm text-decoration-none p-0 me-2" onclick="openEdit(${msg.id}, \`${msg.message.replace(/`/g, '\\`')}\`)">Edit</button>
                    <button class="btn btn-link btn-sm text-danger text-decoration-none p-0" onclick="deleteMessage(${msg.id})">Hapus</button>
                </div>` : '';

            box.innerHTML += `
                <div class="d-flex flex-column mb-3 ${isMine ? 'align-items-end' : 'align-items-start'}">
                    <div class="p-2 px-3 rounded ${isMine ? 'bg-primary text-white' : 'bg-white border'}" style="max-width: 75%;">
                        <p class="mb-1">${msg.message}</p>
                        <small class="${isMine ? 'text-white-50' : 'text-muted'}" style="font-size: 0.7rem;">${time}${editedLabel}</small>
                    </div>
                    ${actions}
                </div>`;
        });
        box.scrollTop = box.scrollHeight;
    }

    // 3. CREATE MESSAGE: POST /api/chat/{userId}/messages
    document.getElementById('send-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('message-input');
        if (!input.value.trim() || !activeUserId) return;

        await fetch(`/api/chat/${activeUserId}/messages`, {
            method: 'POST',
            headers,
            body: JSON.stringify({ message: input.value })
        });
        input.value = '';
        loadMessages();
    });

    // 4. UPDATE MESSAGE: PUT /api/chat/messages/{id}
    function openEdit(id, text) {
        document.getElementById('edit-message-id').value = id;
        document.getElementById('edit-message-input').value = text;
        editModal.show();
    }

    document.getElementById('edit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('edit-message-id').value;
        const message = document.getElementById('edit-message-input').value;

        const res = await fetch(`/api/chat/messages/${id}`, {
            method: 'PUT',
            headers,
            body: JSON.stringify({ message })
        });
        const json = await res.json();
        editModal.hide();
        showAlert(json.message);
        loadMessages();
    });

    // 5. DELETE MESSAGE: DELETE /api/chat/messages/{id}
    async function deleteMessage(id) {
        if (!confirm('Hapus pesan ini?')) return;
        const res = await fetch(`/api/chat/messages/${id}`, {
            method: 'DELETE',
            headers
        });
        const json = await res.json();
        showAlert(json.message);
        loadMessages();
    }

    loadUsers();
</script>
</body>
</html>