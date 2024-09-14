<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id'); // первичный ключ id сообщения
            $table->string('sender_id'); // имя отрпавителя
            $table->string('addressee');// имя получателя
            $table->string('chat_id');// id чата из которого это сообщение
            $table->text('message');// текст этого сообщения
            $table->string('key_string');// строрка с зашифрованным симметричным ключом
            $table->timestamps(); // временные метки created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
