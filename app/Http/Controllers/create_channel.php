<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use Illuminate\Support\Facades\Auth;
class create_channel extends Controller
{
    public function get_channels (Request $request){
        $channels = Channel::where('admins',Auth::user()->id)->get();
        return response()->json(['message' => 'Данные успешно получены!', 'channels' => $channels]);
    }
}
