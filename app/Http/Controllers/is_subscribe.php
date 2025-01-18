<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
class is_subscribe extends Controller
{
    public function check_subscribe(Request $request){
        $is_subscription = [];
        foreach ($request['data'] as $channel_id) {
            $sub = Subscription::where('user_id', Auth::user()->id )->where('channel_id', $channel_id)->get();
            if($sub->count() > 0 ){
                $is_subscription[$channel_id] =  true;
            }
            else {
                $is_subscription[$channel_id] =  false;
            }
        }
        return response()->json(['message' => 'Данные успешно получены!', 'data' => $is_subscription]);
    }
}
