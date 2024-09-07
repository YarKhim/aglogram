<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileUserShow extends Controller
{
    public function getUserName()
    {
        $value = request('id');
        return view('user_profile', ['value' => $value]);
    }
}
