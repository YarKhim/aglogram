<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileUserShow extends Controller
{
    public function getUserName()
    {
        $username = request('id');
        $user = User::where('username', $username)->first();
        return view('user_profile', ['user' => $user]);
    }
}
