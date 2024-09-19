<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class get_chat_messages extends Controller
{
    public function get_messages_from_chat( Request $request) {
        // dd($request->id);
        $this_user_id = Auth::user()->id;
        $second_user = $request->id;
        $users_chats = Chat::where('creator', $this_user_id)->where('invted', $second_user)->orWhere('invted', $this_user_id)->where('creator', $second_user)->first();

        return response()->json(['success' => $users_chats->id]);
    }
}
