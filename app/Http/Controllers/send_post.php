<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
class send_post extends Controller
{
    public function save_post(Request $request){
        dump($request->text);
        return response()->json(['message' => 'Данные успешно получены!', 'data' => $request]);
    }
}
