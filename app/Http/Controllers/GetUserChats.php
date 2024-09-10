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
        $users_whith_chats = [];
        $users_chats = Chat::where('creator', $currentUser->id)->where('chat_started',true)->orWhere('invted', $currentUser->id)->where('chat_started',true)->get();
        // $u = [];
        // $chat_data = [];
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
            $chats_data[] = $chat_data;
            // dump($USER);
        }

        // dd($chats_data);
        return response()->json(['сhats' => $chats_data, 'chats_count', $users_chats->count()]);
    }
}
