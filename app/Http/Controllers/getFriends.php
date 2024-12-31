<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FriendsPair;
use Illuminate\Support\Facades\Auth;
// use App\Facades
class getFriends extends Controller
{
    public function getUserFriends(Request $request){
        $this_user_id  = Auth::user()->id;
        $pairs_friend  = FriendsPair::all();
        $true_pairs = [];
        foreach ($pairs_friend as $key) {
            if($key->invited == $this_user_id || $key->creator == $this_user_id){
                if($key->invited == $this_user_id){
                    $user = User::where('id', $key->creator)->first();
                    $true_pairs[] = $user;
                }
                if($key->creator == $this_user_id){
                    $user = User::where('id', $key->invited)->first();
                    $true_pairs[] = $user;
                }

            }
        }
        return response()->json(['message' => 'Данные успешно получены!', 'friends'=> $true_pairs]);
    }
}
