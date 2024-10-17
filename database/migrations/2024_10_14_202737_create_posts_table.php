<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // المفتاح الأساسي
            $table->string('title'); // عنوان المنشور
            $table->text('content'); // محتوى المنشور
            //$table->unsignedBigInteger('user_id'); // معرف المستخدم الذي نشر المنشور
            $table->timestamps(); // العمودين created_at و updated_at

            // إضافة علاقة (Foreign Key) مع جدول users
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
