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
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('url_origin');
            $table->dropColumn('url_mobile');
            $table->dropColumn('url_tablet');
            $table->dropColumn('url_desktop');
            $table->string('url');
            $table->string('type')->nullable()->change();
            $table->string('size')->nullable()->change();
            $table->string('alt')->nullable();
            $table->string('suggest')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('url_origin');
            $table->string('url_mobile');
            $table->string('url_tablet');
            $table->string('url_desktop');
            $table->dropColumn('url');
            $table->dropColumn('alt');
            $table->dropColumn('suggest');
        });
    }
};
