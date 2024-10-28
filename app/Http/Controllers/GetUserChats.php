<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GetUserChats extends Controller
{
    public function getChats()
    {
        // Обработка запроса и возвращение ответа
        $chats_data = [];
        $currentUser = auth()->user();
        // dump($currentUser);

        $users_whith_chats = [];
        $chat_id = null;
        $users_chats = Chat::where('creator', $currentUser->id)
            ->where('chat_started', true)
            ->orWhere('invted', $currentUser->id)
            ->where('chat_started', true)
            ->get();
        $users_with_chats_data = [];
        // dump($users_chats);
        // $u = [];
        // $chat_data = [];
        foreach ($users_chats as $user) {
            $id_second_user = null;
            if ($user['creator'] == $currentUser->id) {
                $id_second_user = User::where('id', $user['invted'])->first();
            } else {
                if ($user['invted'] == $currentUser->id) {
                    $id_second_user = User::where('id', $user['creator'])->first();
                }
            }

            $users_whith_chats_data[] = $id_second_user;
        }

        for ($i = 0; $i < $users_chats->count(); $i++) {
            $users_whith_chats[] = $users_chats[$i]->creator;
        }
        foreach ($users_whith_chats as $user) {
            $chat_data = [];
            $USER = User::where('id', $user)->first();
            $chat_data['name'] = $USER->name;
            $chat_data['lastname'] = $USER->lastname;
            $chat_data['username'] = $USER->username;
            $chat_data['avatar'] = $USER->avatar;
            $chat_data['id'] = $USER->id;
            $chat_data['isOnline'] = $USER->isOnline;
            $chats_data[] = $chat_data;
            // dump($USER);
        }

        // dd($chats_data);
        return response()->json(['сhat' => $chats_data, 'chats_count' => $users_chats->count(), 'сhats' => $users_whith_chats_data, 'chat_id' => $users_chats]);
    }
}
