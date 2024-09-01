<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserSearch extends Controller
{
    public function submit(Request $request)
    {
        // Валидация данных
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $username = $request->input('name');
        $users = User::where('name', $username)->get();
        $count = $users->count();
        // $user = User::where('name', 'LIKE', '%' . $username . '%')->get();

        // Проверка, найден ли пользователь
        if ($users) {
            return view('friends', ['users' => $users, 'count' => $count]);
        } else {
            return view('friends', ['users' => 'Пользователь не найден']);
            // return redirect()->back()->with('message', 'Пользователь не найден');
        }
    }
}
