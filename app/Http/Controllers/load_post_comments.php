<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
class load_post_comments extends Controller
{
    public function load_comments(Request $request){
        $perPage = 3;
        $page = $request->input('page', 1);
        $query = Comment::query();
        if ($request->has('post_id')) {
            $query->where('post_id', $request->input('post_id'))->where('type_comment', 'post_comment');
            // $author = User::where('id', $request->input('user'));
        }
        $posts = $query->orderBy('created_at', 'desc')->paginate($perPage, ['author_id'], 'page', $page);
        $author_id_arr = $posts->items();
        $users = [];
        foreach ($author_id_arr as $author) {
            $users[$author->author_id] = User::where('id', $author->author_id)->first();
        }

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return response()->json(['message' => 'Данные успешно получены!', 'data' => $data, 'users' => $users]);
    }
}
