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
            $table->dropColumn('content');
            $table->dropColumn('content_draft');
            $table->dropColumn('content_vi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->text('content')->nullable(true);
            $table->text('content_draft')->nullable(true);
            $table->text('content_vi')->nullable(true);
        });
    }
};
