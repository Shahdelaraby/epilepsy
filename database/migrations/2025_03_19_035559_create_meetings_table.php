<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meeting_room')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('time_zone')->default('UTC');
            $table->string('link')->nullable();
            $table->enum('meeting_mode', ['audio', 'video'])->default('audio');
            $table->enum('meeting_category', ['schedule', 'communication'])->default('schedule');
            $table->enum('schedule', ['yes', 'no'])->default('no');
            $table->enum('status', ['pending', 'live', 'end', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
