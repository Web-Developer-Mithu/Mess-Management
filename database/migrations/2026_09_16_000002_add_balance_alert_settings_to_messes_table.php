<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messes', function (Blueprint $table) {
            $table->decimal('balance_alert_threshold', 12, 2)->nullable()->after('logo');
            $table->text('balance_alert_comment')->nullable()->after('balance_alert_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('messes', function (Blueprint $table) {
            $table->dropColumn(['balance_alert_threshold', 'balance_alert_comment']);
        });
    }
};
