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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->after('name');
            $table->string('meta_title')->nullable()->after('slug');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->renameColumn('feature_img', 'featured_img');
            $table->string('banner_img')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->renameColumn('featured_img', 'feature_img');
            $table->dropColumn('banner_img');
        });
    }
};
