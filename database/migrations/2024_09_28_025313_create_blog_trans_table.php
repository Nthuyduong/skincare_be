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
        Schema::create('blog_trans', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->bigInteger('blog_id');
            $table->string('title')->nullable(true);
            $table->string('slug')->nullable(true);
            $table->text('summary')->nullable(true);
            $table->string('tag')->nullable(true);
            $table->text('meta_title')->nullable(true);
            $table->text('meta_description')->nullable(true);
            $table->string('excerpt', 500)->nullable(true);
            $table->string('suggest')->nullable(true);
            $table->text('content')->nullable(true);
            $table->text('content_draft')->nullable(true);
            $table->integer('estimate_time')->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('category_trans', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->bigInteger('category_id');
            $table->string('name')->nullable(true);
            $table->string('slug')->nullable(true);
            $table->string('meta_title')->nullable(true);
            $table->string('meta_description')->nullable(true);
            $table->text('description')->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ingredients_trans', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->bigInteger('ingredient_id');
            $table->string('name')->nullable(true);
            $table->string('slug')->nullable(true);
            $table->string('meta_title')->nullable(true);
            $table->string('meta_description')->nullable(true);
            $table->string('description')->nullable(true);
            $table->text('content')->nullable(true);
            $table->string('suggest')->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ingredient_details_trans', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->bigInteger('detail_id')->nullable(true);
            $table->string('name')->nullable(true);
            $table->text('content')->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_trans');
        Schema::dropIfExists('category_trans');
        Schema::dropIfExists('ingredients_trans');
        Schema::dropIfExists('ingredient_details_trans');
    }
};
