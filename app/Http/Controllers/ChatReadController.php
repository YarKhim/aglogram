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
        // function getChatId($user1Id, $user2Id)
        // {
        //     return Chat->where(function ($query) use ($user1Id, $user2Id) {
        //         $query->where('creator', $user1Id)->where('invted', $user2Id);
        //     })
        //         ->orWhere(function ($query) use ($user1Id, $user2Id) {
        //             $query->where('invted', $user2Id)->where('creator', $user1Id);
        //         })
        //         ->get(); // Получаем только ID чата
        // }
        // $all_chats_with_user =  getChatId($this_user_id, $this_user_id);
        // dump($all_chats_with_user);

        // $all_chats_with_user = getChatId($this_user_id, $this_user_id);
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
        return response()->json(['chats_id' => $chats_with_unread_message, 'last_messsages' => $last_messages]);
    }
}
