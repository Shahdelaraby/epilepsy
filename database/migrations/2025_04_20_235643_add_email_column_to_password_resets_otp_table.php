<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('password_resets_otp', function (Blueprint $table) {
        $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
    });
}

public function down(): void
{
    Schema::table('password_resets_otp', function (Blueprint $table) {
        $table->dropColumn('otp_expires_at');
    });
}
};
