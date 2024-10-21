<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatReadController extends Controller
{
    public function get_unread_chats()
    {
        $this_user_id = Auth::user()->id;
        $chats_with_unread_message = [];
        $last_messages = [];
        $last_messages_all = [];
        $all_chats = Chat::where('creator', $this_user_id)->orWhere('invted', $this_user_id)->get();
        // dd($all_chats);
        foreach ($all_chats as $chat) {
            $last_message = Message::where('chat_id', $chat->id)
                ->orderBy('created_at', 'desc')
                ->first();
            // dump($last_message);
            if ($last_message) {
                // dd($last_message);
                // dd($last_message->addressee);
                $addr_id = $last_message->addressee;
                // dd($addr);
                // $last_message_arr = [];
                $addr = User::where('id', $addr_id)->first();
                $last_message_arr[] = [
                    'addressee' => $addr,
                    'last_message' => $last_message,
                ];
                $last_messages_all[$chat->id] = $last_message_arr[0];
            }
            $last_message_arr = [];
        }
        $all_messages = Message::where('addressee', $this_user_id)->where('sender_id', '!=', $this_user_id)->where('isRead', 0)->get();
        $full_messages = Message::where('sender_id', $this_user_id)->get();
        foreach ($all_messages as $message) {
            $earliestMessage = Message::where('chat_id', $message->chat_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $last_messages[$message->chat_id] = ['message' => $earliestMessage, 'sender' => User::where('id', $earliestMessage->addressee)->first()];
            if (!array_key_exists($message->chat_id, $chats_with_unread_message)) {
                $chats_with_unread_message[$message->chat_id] = 1;
            } else {
                $chats_with_unread_message[$message->chat_id] += 1;
            }
            // dump($message->isRead, $message->chat_id);W
        }
        return response()->json(['chats_id' => $chats_with_unread_message, 'last_messsages' => $last_messages_all]);
    }
}
