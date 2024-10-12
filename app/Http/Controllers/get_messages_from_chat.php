<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class get_messages_from_chat extends Controller
{
    public function get_all_messages_from_chat(Request $request)
    {
        dump($request->chat_id);


        return response()->json(['response' => 123]);
    }
}
