<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->date('birthday')->nullable();
            $table->string('country')->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->string('avatar')->nullable(); // للصورة الشخصية
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'phone', 'birthday', 'country', 'gender', 'avatar']);
        });
    }
};
