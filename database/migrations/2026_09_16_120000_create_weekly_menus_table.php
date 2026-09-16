<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->text('menu')->nullable();
            $table->timestamps();
            $table->unique(['mess_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_menus');
    }
};