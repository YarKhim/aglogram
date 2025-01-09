<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
class isLikeSent extends Controller
{
    public function islikesent(Request $request){
        $like = Like::where('post_id',$request->post_id)->where('sender_id',$request->user_id)->where('type_like',$request->type_like)->get()->count();
        if($like==1){
            return response()->json(['message' => 'Данные успешно получены!', 'request_state' => true]);
        }
        // Like::where()
        return response()->json(['message' => 'Данные успешно получены!', 'request_state' => false]);
        // echo 1;
    }
}
