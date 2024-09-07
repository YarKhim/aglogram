<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileUserShow extends Controller
{
    public function getUserName()
    {
        $user = User::find(request('id'));
        return view('user_profile', ['user' => $user]);
    }
}
