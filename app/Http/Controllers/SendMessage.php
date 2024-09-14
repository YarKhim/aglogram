<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SendMessage extends Controller
{
    public function send_message(Request $request)
    {
        $this_user = Auth::user();
        $userId1 = $this_user->id;
        $userId2 = intval($request->addressee);
        // dd($request->message);
        if (is_array($request->message)) {
            $data = implode('/_<message_hr_>_/', $request->message); // Преобразуем массив в строку
        }
        // dd($data);
        $this_chat = Chat::where(function ($query) use ($userId1, $userId2) {
            $query->where('creator', $userId1)
                ->where('invted', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('creator', $userId2)
                ->where('invted', $userId1);
        })->first();
        // dd($this_chat->id);
        $this_chat_id = $this_chat->id;
        $message  = Message::create([
            'sender_id' => $this_user->id,
            'addressee' => $request->addressee,
            'chat_id' => $this_chat_id,
            'message' => $data,
            'key_string' => $request->encrypted_key,

        ]);
        // $message->save();


        return response()->json(['message' => $request->message]);
    }
}
