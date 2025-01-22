<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['author_id', 'text', 'photos', 'likes_count', 'comments_count', 'views', 'type_post', 'video_name', 'type_media'];
}
