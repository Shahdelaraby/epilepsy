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
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // عمود ID أساسي
            $table->string('name');
            $table->text('description');
            $table->timestamp('startDateTime');
            $table->timestamp('endDateTime');
            $table->json('attendees')->nullable();  // عمود الحضور
            $table->string('meet_link')->nullable(); // رابط Google Meet
            $table->timestamps();  // للحفاظ على تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
