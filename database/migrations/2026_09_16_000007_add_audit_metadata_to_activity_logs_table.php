<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('mess_id')->nullable()->after('user_id')->index();
            $table->string('device_type')->nullable()->after('ip_address');
            $table->string('browser')->nullable()->after('device_type');
            $table->string('platform')->nullable()->after('browser');
            $table->string('location')->nullable()->after('platform');
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['action', 'created_at']);
            $table->dropColumn(['mess_id', 'device_type', 'browser', 'platform', 'location']);
        });
    }
};
