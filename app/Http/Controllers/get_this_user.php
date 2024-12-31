<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class get_this_user extends Controller
{
    public function getThisUser(Request $request){
        $this_user_id = Auth::user()->id;
        return response()->json(['this_user_id'=> $this_user_id]);
    }
}
