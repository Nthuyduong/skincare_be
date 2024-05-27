<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateImageUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // run command: php artisan migrate-blog-content-to-blog-details
    protected $signature = 'migrate-blog-image-url';

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
            $blogDetail = \App\Models\BlogDetail::where('blog_id', $blog->id);
            
            $newContent = str_replace('https://app.radiance-aura.blog', 'https://api.radiance-aura.blog', $blogDetail->content);
            $newContentDraft =  str_replace('https://app.radiance-aura.blog', 'https://api.radiance-aura.blog', $blogDetail->content_draft);

            $blogDetail->content = $newContent;
            $blogDetail->content_draft = $newContentDraft;

            $blogDetail->save();
        }
    }
}
