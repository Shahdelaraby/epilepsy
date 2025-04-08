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
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('meeting_id')->nullable();  // إضافة عمود meeting_id
            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade'); // مفتاح خارجي
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);  // حذف العلاقة باستخدام اسم المفتاح الخارجي
            $table->dropColumn('meeting_id');  // حذف العمود
        });
    }
};
