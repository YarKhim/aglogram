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
        $currentUser = auth()->user();
        // $username = request('id');
        $users_chats = Chat::where('creator', $currentUser->id)->orWhere('invted',$currentUser->id )->get();
        return response()->json(['message' => $users_chats]);
    }
}
