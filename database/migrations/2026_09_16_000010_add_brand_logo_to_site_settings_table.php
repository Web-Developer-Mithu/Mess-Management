<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'brand_logo_url')) {
                $table->string('brand_logo_url')->nullable()->after('brand_name');
            }

            if (! Schema::hasColumn('site_settings', 'brand_logo_path')) {
                $table->string('brand_logo_path')->nullable()->after('brand_logo_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'brand_logo_path')) {
                $table->dropColumn('brand_logo_path');
            }

            if (Schema::hasColumn('site_settings', 'brand_logo_url')) {
                $table->dropColumn('brand_logo_url');
            }
        });
    }
};
