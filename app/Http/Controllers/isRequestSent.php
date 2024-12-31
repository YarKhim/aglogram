<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FriendRequest;
use Illuminate\Support\Facades\Auth;
class isRequestSent extends Controller
{
    public function isreqSent(Request $request)
    {
        $validatedData = $request->validate([
            'data' => 'required|string|max:255',
        ]);
        $user_id = json_decode($validatedData['data']);
        $this_user_id = Auth::user()->id;
        $request = FriendRequest::where('sender', $this_user_id)->where('addresee', $user_id)->first();
        if(isset($request)){
            return response()->json(['message' => 'Данные успешно получены!', 'request_state' => true]);
        }
        else{
            return response()->json(['message' => 'Данные успешно получены!', 'request_state' => false]);
        }
    }
}
