<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class FriendRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'addresee',
        'sender',
        'confirmed',
    ];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            // Проверяем существование обратного запроса
            if (self::where('sender', $request->addressee)
                ->where('addressee', $request->sender)
                ->exists()) {
                throw new Exception('Запрос дружбы уже существует.');
            }
        });
    }
}
