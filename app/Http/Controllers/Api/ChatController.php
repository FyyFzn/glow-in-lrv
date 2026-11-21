<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // GET /api/chat-users : Daftar kontak user
    public function users()
    {
        $users = User::where('id', '!=', Auth::id())->select('id', 'name', 'username')->get();
        return response()->json(['status' => 'success', 'data' => $users]);
    }

    // GET /api/messages?user_id={id} : READ riwayat chat dengan user tertentu
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Parameter user_id wajib diisi'], 400);
        }

        $messages = Message::where(function ($query) use ($userId) {
                $query->where('sender_id', Auth::id())->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $messages]);
    }

    // POST /api/messages : CREATE kirim pesan baru
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pesan terkirim', 'data' => $message], 201);
    }

    // GET /api/messages/{id} : READ detail 1 pesan
    public function show($id)
    {
        $message = Message::findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $message]);
    }

    // PUT /api/messages/{id} : UPDATE edit pesan
    public function update(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $message = Message::where('id', $id)->where('sender_id', Auth::id())->firstOrFail();
        $message->update([
            'message' => $request->message,
            'is_edited' => true,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pesan diperbarui', 'data' => $message]);
    }

    // DELETE /api/messages/{id} : DELETE hapus pesan
    public function destroy($id)
    {
        $message = Message::where('id', $id)->where('sender_id', Auth::id())->firstOrFail();
        $message->delete();

        return response()->json(['status' => 'success', 'message' => 'Pesan dihapus']);
    }
}