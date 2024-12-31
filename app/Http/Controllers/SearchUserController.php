<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
// use L
class SearchUserController extends Controller
{
    public function search_users(Request $request)
    {
        $validatedData = $request->validate([
            'data' => 'required|string|max:255',
        ]);
        $res = [];
        $data = json_decode($validatedData['data']);
        foreach ($data as $key) {
            $users = User::whereRaw('SOUNDEX(name) = SOUNDEX(?)', [$key])->get();
            if ($users->count() > 0) {
                $res[] = $users;
            }
            $users = User::whereRaw('SOUNDEX(lastname) = SOUNDEX(?)', [$key])->get();
            if ($users->count() > 0) {
                $res[] = $users;
            }
        }
        foreach ($data as $key) {
            $users = User::where('username', trim($key, '@'))->get();
            if ($users->count() > 0) {
                $res[] = $users;
            }
        }
        return response()->json(['message' => 'Данные успешно получены!', 'data' => $res]);
    }
}
