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
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('meta_title')->nullable(true);
            $table->string('meta_description')->nullable(true);
            $table->string('banner_img')->nullable(true);
            $table->string('excerpt')->nullable(true);
            $table->string('content_vi')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->dropColumn('banner_img');
            $table->dropColumn('excerpt');
            $table->dropColumn('content_vi');
        });
    }
};
