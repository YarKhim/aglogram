<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
class ReadMessage extends Controller
{
    public function reading_message(Request $request)
    {
        $message = Message::where('message_id', $request->message_id);
        if ($message) {
            $message->update(['isRead' => true]);
            return response()->json(['message' => 'Сообщение прочитанно']);
        }
        return response()->json(['message' => 'Сообщение не найдено('], 404);
    }
}
