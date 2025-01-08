<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
class load_posts extends Controller
{
    public function load_post(Request $request){
        $perPage = 3; // количество элементов на странице
        $page = $request->input('page', 1);
        $query = Post::query();
        if ($request->has('user')) {
            $query->where('author_id', $request->input('user'));
            $author = User::where('id', $request->input('user'));
        }
        // $posts = $query->orderBy('created_at', 'desc')->get();

        // foreach ($posts as $post) {
        //     # code...
        //     dump($post);
        // }
        $data = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        // foreach ($data as $value) {
        //     dump($value);
        //     # code...
        // }
        // $data->author = $author;
        // return response()->json($data);
        return response()->json(['message' => 'Данные успешно получены!', 'data' => $data]);
    }
}
