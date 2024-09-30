<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BlogServiceManagement\BlogManagementModelProxy;
use App\Services\CategoryServiceManagement\CategoryManagementModelProxy;
use App\Models\Category;
use App\Models\Blog;

class MigrateDataWithLocale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-data-with-locale';

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
        $locales = ['vi'];

        $blogManagementModelProxy = resolve(BlogManagementModelProxy::class);
        $categoryManagementModelProxy = resolve(CategoryManagementModelProxy::class);

        foreach ($locales as $locale) {
            Blog::chunk(100, function ($blogs) use ($blogManagementModelProxy, $locale) {
                foreach ($blogs as $blog) {
                    $id = $blog->id;
                    $detail = $blog->detail;
                    $data = [
                        'title' => $locale . '-' . $blog->title,
                        'summary' => $locale . '-' . $blog->summary,
                        'tag' => $locale . '-' . $blog->tag,
                        'slug' => $locale . '-' . $blog->slug,
                        'meta_title' => $locale . '-' . $blog->meta_title,
                        'meta_description' => $locale . '-' . $blog->meta_description,
                        'excerpt' => $locale . '-' . $blog->excerpt,
                        'suggest' => $locale . '-' . $blog->suggest,
                        'content' => $locale . '-' . $detail->content,
                    ];
                    $blogManagementModelProxy->createOrUpdateBlogTran($id, $locale, $data);
                }
            });
            Category::chunk(100, function ($categories) use ($categoryManagementModelProxy, $locale) {
                foreach ($categories as $category) {
                    $id = $category->id;
                    $data = [
                        'name' => $locale . '-' . $category->name,
                        'slug' => $locale . '-' . $category->slug,
                        'meta_title' => $locale . '-' . $category->meta_title,
                        'meta_description' => $locale . '-' . $category->meta_description,
                        'description' => $locale . '-' . $category->description,
                    ];
                    $categoryManagementModelProxy->createOrUpdateCategoryTran($id, $locale, $data);
                }
            });
        }
        return 0;
    }
}
