<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatReadController extends Controller
{
    public function get_unread_chats()
    {
        $this_user_id = Auth::user()->id;
        $chats_with_unread_message = [];
        $last_messages = [];
        $all_messages = Message::where('addressee', $this_user_id)->where('sender_id', '!=', $this_user_id)->where('isRead', 0)->get();
        // dump($all_messages);
        foreach ($all_messages as $message) {
            $earliestMessage = Message::where('chat_id', $message->chat_id)
                ->orderBy('created_at', 'asc')
                ->first();
            $last_messages[$message->chat_id] = ['message' => $earliestMessage, 'sender' => User::where('id', $earliestMessage->addressee)->first()];
            if (!array_key_exists($message->chat_id, $chats_with_unread_message)) {
                $chats_with_unread_message[$message->chat_id] = 1;
            } else {
                $chats_with_unread_message[$message->chat_id] += 1;
            }
            // dump($message->isRead, $message->chat_id);W

        }
        // dump($chats_with_unread_message);
        // dump(['chats_id' =>  $chats_with_unread_message, 'last_messsages' => $last_messages]);
        return response()->json(['chats_id' =>  $chats_with_unread_message, 'last_messsages' => $last_messages]);
    }
}
