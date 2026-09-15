<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('type')->default('meal')->after('note');
            $table->boolean('is_fixed')->default(false)->after('type');
            $table->json('member_ids')->nullable()->after('is_fixed');
            $table->json('member_adjustments')->nullable()->after('member_ids');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_fixed', 'member_ids', 'member_adjustments']);
        });
    }
};
