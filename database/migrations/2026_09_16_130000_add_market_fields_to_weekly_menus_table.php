<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_menus', function (Blueprint $table) {
            $table->string('market_person')->nullable()->after('menu');
            $table->text('market_condition')->nullable()->after('market_person');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_menus', function (Blueprint $table) {
            $table->dropColumn(['market_person', 'market_condition']);
        });
    }
};