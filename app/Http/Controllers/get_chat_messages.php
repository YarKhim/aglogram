<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class get_chat_messages extends Controller
{
    public function get_messages_from_chat(Request $request)
    {
        $this_user = Auth::user();
        $chat = Chat::where('id', $request->chat_id)->first();
        $id_second_user = 1;
        // dump($chat->creator);
        // dump($chat->invted);
        if ($chat->creator != $chat->invted) {
            if ($chat->creator == $this_user->id) {
                $id_second_user = $chat->invted;
            } else {
                $id_second_user = $chat->creator;
            }
            $second_user = User::where('id', $id_second_user)->first();
        } else {
            $second_user = $this_user;
        }
        $this_user_id = $this_user->id;
        $my_messages = [];
        $not_my_messages = [];
        $all_messages = Message::where('chat_id', $request->chat_id)->get();
        foreach ($all_messages as $message) {
            if ($message->sender_id == $this_user_id) {
                $my_messages[] = $message;
            } else {
                $not_my_messages[] = $message;
            }
        }
        // dump($second_user);
        // dump($this_user);
        return response()->json(['all_messages' => $all_messages, 'this_user' => $this_user, 'second_user' => $second_user]);
    }
}
