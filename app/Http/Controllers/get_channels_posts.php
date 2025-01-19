<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
class get_channels_posts extends Controller
{
    public function load(Request $request)
    {
        // Получаем количество постов для загрузки из запроса (по умолчанию 10)
        $limit = $request->input('limit', 10);
        $page = $request->input('page');
        // Создаем базовый запрос
        $query = Post::query();
        $query->where('type_post', 'channel_post')->where('author_id', $request->input('current_channel'));

        $posts = $query->orderBy('created_at', 'desc')->paginate($limit, ['id'], 'page', $page);
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
        // Применяем сортировку или другие условия, если необходимо
        $query->orderBy('created_at', 'desc');

        // Получаем посты с пагинацией
        $posts = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json(['message' => 'Данные успешно получены!', 'data' => $posts, 'posts' => $posts, 'liked_posts'=>$liked_posts]);
    }
}
