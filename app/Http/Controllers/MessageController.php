<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function get_all_messages_from_chat(Request $request)
    {
        dump($request->input('chat_id'));
        return response()->json(['message' => 12]);
    }
}
