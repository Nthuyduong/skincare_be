<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0 ; $i < 50 ; $i++) {
            $blog = new Blog();
            $blog->title = 'gios dua cay cai ve troi ' . $i;
            $blog->content = 'gios dua cay cai ve troi ' . $i;
            $blog->content_draft = 'gios dua cay cai ve troi ' . $i;
            $blog->summary = 'gios dua cay cai ve troi ' . $i;
            $blog->tag = 'news';
            $blog->slug = 'gios-dua-cay-cai-ve-troi-' . $i;
            $blog->status = Blog::STATUS_DRAFT;
            $blog->view_count = 0;
            $blog->comment_count = 0;
            $blog->featured_img = null;
            $blog->publish_date = now();
            $blog->save();
        }
    }
}
