<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateBlogContentToBlogDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // run command: php artisan migrate-blog-content-to-blog-details
    protected $signature = 'migrate-blog-content-to-blog-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $blogs = \App\Models\Blog::all();
        foreach ($blogs as $blog) {
            $blogDetail = new \App\Models\BlogDetail();
            $blogDetail->blog_id = $blog->id;
            $blogDetail->content = $blog->content;
            $blogDetail->content_draft = $blog->content_draft;
            $blogDetail->save();
        }
    }
}
