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
            $table->text('summary');
            $table->string('tag')->nullable(true);;
            $table->integer('status')->default(0);
            $table->string('slug')->unique();
            $table->string('author');
            $table->integer('view_count');
            $table->integer('share_count');
            $table->integer('comment_count');
            $table->dateTime('publish_date');
            $table->string('featured_img');
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
