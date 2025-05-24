<?php

namespace App\Http\Controllers\Web;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        $conversionId = $senderId < $receiverId
            ? "{$senderId}-{$receiverId}"
            : "{$receiverId}-{$senderId}";

        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'conversion_id' => $conversionId,
            'content' => $request->content,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ]);
    }

    public function getMessagesWith($receiver_id)
    {
        $sender_id = Auth::id();

        $conversionId = $sender_id < $receiver_id
            ? "{$sender_id}-{$receiver_id}"
            : "{$receiver_id}-{$sender_id}";

        $messages = Message::with('sender:id,name')
            ->where('conversion_id', $conversionId)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}
