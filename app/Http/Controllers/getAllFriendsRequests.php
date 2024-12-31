<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FriendRequest;
use App\Models\User;
class getAllFriendsRequests extends Controller
{
    public function getFriendRequests(Request $request)
    {
        $this_user_id = Auth::user()->id;
        $requests = FriendRequest::where('addresee', $this_user_id)->get();
        $users = [];
        if($requests->count() > 0){
            foreach ($requests as $req) {
                $user = User::where('id', $req->sender)->first();
                $users[] = $user;
            }
        }
        return response()->json(['message' => 'Данные успешно получены!', 'user_sender_requests' => $users]);
    }
}
