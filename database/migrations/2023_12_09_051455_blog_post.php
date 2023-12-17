<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema:: create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->text('summary')->nullable(true);
            $table->string('tag')->nullable(true);;
            $table->integer('status')->default(0);
            $table->string('slug')->unique();
            $table->string('author')->nullable(true);
            $table->integer('view_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->dateTime('publish_date')->nullable(true);
            $table->string('featured_img')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
