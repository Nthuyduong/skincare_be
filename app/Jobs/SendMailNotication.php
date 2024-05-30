<?php

namespace App\Jobs;

use App\Helpers\MailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Subscribes;
use App\Models\Blog;
use App\Models\MailSetting;

class SendMailNotication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $blogId;
    /**
     * Create a new job instance.
     */
    public function __construct($blogId)
    {
        $this->queue = 'SendMailNotication';
        $this->blogId = $blogId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendMailNotication');
        $blog = Blog::with('categories')
            ->where('id', $this->blogId)
            ->first();
        $setting = MailSetting::where('type', MailSetting::TYPE_NOTIFICATION)->first();

        $content = $setting->content;

        $content = str_replace('[[title]]', $blog->title, $content);
        $content = str_replace('[[summary]]', $blog->content, $content);
        $content = str_replace('[[link]]', config('app.fe_url') . "/article/" . rawurlencode($blog->slug), $content);
        $content = str_replace('[[image]]', config('app.url') . "/storage/desktop/" . rawurlencode($blog->featured_img), $content);
        $content = str_replace('[[banner]]', config('app.url') . "/storage/desktop/" . rawurlencode($blog->banner_img), $content);
        $publishDate = date('d/m/Y', strtotime($blog->publish_date));
        $content = str_replace('[[date]]', $publishDate, $content);
        $content = str_replace('[[author]]', $blog->author, $content);
        $content = str_replace('[[estimateTime]]', $blog->estimate_time, $content);
        $categoriesName = $blog->categories->pluck('name')->toArray();
        $content = str_replace('[[category]]', implode(', ', $categoriesName), $content);
        $content = str_replace('[[excerpt]]', $blog->excerpt, $content);

        Subscribes::chunk(100, function ($subcribes) use ($setting, $content){
            foreach ($subcribes as $subcribe) {

                $content = str_replace('[[name]]', $subcribe->name, $content);
                $content = str_replace('[[now]]', date('d M Y'), $content);

                $job = new SendMailJob($subcribe->email, $setting->title, $content);
                dispatch($job);
            }
        });
    }
}
