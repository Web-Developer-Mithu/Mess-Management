<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->onDelete('cascade');
            $table->string('month');
            $table->decimal('meal_rate', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['mess_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
