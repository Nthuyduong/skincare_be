<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateBlogContentToDraf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-blog-content-to-draf';

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
        $blogsDetail = \App\Models\BlogDetail::all();
        foreach ($blogsDetail as $blogDetail) {
            $blogDetail->content_draft = $blogDetail->content;
            $blogDetail->save();
        }
    }
}
