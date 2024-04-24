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
        $blog = Blog::find($this->blogId);
        $setting = MailSetting::where('type', MailSetting::TYPE_NOTIFICATION)->first();
        $content = $blog->content;

        $content = str_replace('[[title]]', $blog->title, $content);
        $content = str_replace('[[summary]]', $blog->content, $content);
        $content = str_replace('[[link]]', config('app.fe_url') . "/article/" . $blog->slug, $content);
        $content = str_replace('[[image]]', config('app.url') . "/storage/desktop/" . $blog->featured_img, $content);
        $content = str_replace('[[banner]]', config('app.url') . "/storage/desktop/" . $blog->banner_img, $content);

        Subscribes::chunk(100, function ($subcribes) use ($setting, $content){
            foreach ($subcribes as $subcribe) {
                $job = new SendMailJob($subcribe->email, $setting->title, $content);
                dispatch($job);
            }
        });
    }
}
