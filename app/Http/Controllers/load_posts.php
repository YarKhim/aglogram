<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $posts = $query->orderBy('created_at', 'desc')->paginate($perPage, ['id'], 'page', $page);
        $posts_id_arr = $posts->items();
        $posts_id= [];
        foreach ($posts_id_arr as $elem) {
            $posts_id[] = $elem->id;
        }
        $user_id = Auth::user()->id;
        $liked_posts = [];
        foreach ($posts_id as $id) {
            $like_count = Like::where('post_id',$id)->where('sender_id',$user_id)->where('type_like','post_like')->get()->count();
            if($like_count==1){
                $liked_posts[$id] = true;
            }
            else {
                $liked_posts[$id] = false;
            }
            // $liked_posts[$id] =

        }

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
        return response()->json(['message' => 'Данные успешно получены!', 'data' => $data, 'posts' => $posts, 'liked_posts'=>$liked_posts]);
    }
}
