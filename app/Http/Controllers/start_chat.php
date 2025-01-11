<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
class start_chat extends Controller
{
    public function start_users_chat(Request $request){
        // return 1;
        $chat_1 = Chat::where('creator', $request->this_user_id)->where('invted',$request->user_id);
        $chat_2 = Chat::where('invted', $request->this_user_id)->where('creator',$request->user_id);
        if($chat_1->get()->count() == 0){
            $chat = $chat_2->first();
            $chat->chat_started = true;
            $chat->save();
        }
        else {
            $chat = $chat_1->first();
            $chat->chat_started = true;
            $chat->save();
        }
        return response()->json(['message' => 'Данные успешно получены!']);
    }
}
