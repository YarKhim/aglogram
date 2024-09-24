<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetKeys extends Controller
{
    public function get_keys(Request $request)
    {
        $this_user = Auth::user();
        $userId1 = $this_user->id;
        $userId2 = intval($request->addressee);
        $this_chat = Chat::where(function ($query) use ($userId1, $userId2) {
            $query->where('creator', $userId1)
                ->where('invted', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('creator', $userId2)
                ->where('invted', $userId1);
        })->first();
        $addressee_public_key = User::where('id', $userId2)->first()->public_key;
        $addressee_private_key = User::where('id', $userId2)->first()->private_key;
        $this_chat_key = $this_chat->symmetric_chat_key;
        // $this_chat = $this_chat->id;
        return response()->json([
            'public_key' => $addressee_public_key,
            'private_key' => $addressee_private_key,
            'chat_key' => $this_chat_key,
            'chat_id' =>  $this_chat->id
        ]);
    }
}
