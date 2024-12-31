<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchUsers extends Controller
{
    public function search_user_for_friend(Request $request)
    {
        $a = 1;
        dump($request);
        // $data = $request->input('data');
        // $data = json_decode($request->input('data'));
        // dump(json_decode($request->input('data')));
        // dump($request->query('data'));

    }
}
