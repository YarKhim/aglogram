<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
// use

class get_user extends Controller
{
    public function get_user(Request $request)
    {
        $id = $request->input('id');
        $user = User::where('id', $id)->first();
        return response()->json(['success' => true, 'user' => $user]);
    }
}
