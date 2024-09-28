<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyKeysLengthInUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_key', 4096)->change();
            $table->string('private_key', 4096)->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_key')->change(); // Вернуть к значению по умолчанию, если нужно
            $table->string('private_key')->change(); // Вернуть к значению по умолчанию, если нужно
        });
    }
}
