<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
class search_channels extends Controller
{
    public function search (Request $request){

        $validatedData = $request->validate([
            'data' => 'required|string|max:255',
        ]);
        $res = [];
        $data = [$validatedData['data']];
        foreach ($data as $key) {
            $channels = Channel::whereRaw('SOUNDEX(name) = SOUNDEX(?)', [$key])->get();
            if ($channels->count() > 0) {
                $res[] = $channels;
            }
        }
        foreach ($data as $key) {
            $channels = Channel::where('channel_name', trim($key, '@'))->get();
            if ($channels->count() > 0) {
                $res[] = $channels;
            }
        }
        return response()->json(['message' => 'Данные успешно получены!', 'data' => $res]);
    }
    public function get_all_subscriptions (){
        $user_id = Auth::user()->id;
        $all_subscriptions = Subscription::where('user_id',$user_id)->get();
        $channels = [];
        foreach ($all_subscriptions as $subscription) {
            $channel_id = $subscription->channel_id;
            $channels[] = Channel::where('id', $channel_id)->first();
        }
        return response()->json(['channels' => $channels]);
    }
}
