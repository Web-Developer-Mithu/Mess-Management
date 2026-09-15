<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messes', function (Blueprint $table) {
            $table->decimal('total_balance_warning_threshold', 12, 2)->nullable()->after('balance_alert_comment');
            $table->text('total_balance_warning_message')->nullable()->after('total_balance_warning_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('messes', function (Blueprint $table) {
            $table->dropColumn(['total_balance_warning_threshold', 'total_balance_warning_message']);
        });
    }
};
