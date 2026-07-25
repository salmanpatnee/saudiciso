<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cms_process', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable()->after('featured_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_process', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
