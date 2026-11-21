<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Menampilkan daftar user untuk diajak chat
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat.index', compact('users'));
    }

    // READ: Menampilkan percakapan dengan user tertentu
    public function show($userId)
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $activeUser = User::findOrFail($userId);

        $messages = Message::where(function ($query) use ($userId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', compact('users', 'activeUser', 'messages'));
    }

    // CREATE: Menyimpan pesan baru
    public function store(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'message' => $request->message,
        ]);

        return redirect()->route('chat.show', $userId);
    }

    // UPDATE: Mengedit pesan milik sendiri
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::where('id', $id)
                          ->where('sender_id', Auth::id())
                          ->firstOrFail();

        $message->update([
            'message' => $request->message,
            'is_edited' => true,
        ]);

        return back()->with('success', 'Pesan berhasil diubah.');
    }

    // DELETE: Menghapus pesan milik sendiri
    public function destroy($id)
    {
        $message = Message::where('id', $id)
                          ->where('sender_id', Auth::id())
                          ->firstOrFail();

        $message->delete();

        return back()->with('success', 'Pesan berhasil dihapus.');
    }
}