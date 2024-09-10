<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'creator',
        'invted',
        'symmetric_chat_key',
        'chat_started',
    ];
    use HasFactory;
}
