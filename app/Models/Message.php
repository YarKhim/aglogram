<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'messages';
    protected $fillable = [
        'sender_id',
        'addressee',
        'chat_id',
        'message',
        'key_string',
    ];
    // public function send_message(){

    // }
}
