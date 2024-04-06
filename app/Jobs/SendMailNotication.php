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
        $content = 'New blog has been posted: ' . $blog->title;
        $content .= '<br>';
        $content .= '<a href="' . config('app.fe_url') . "/article/" . $blog->slug . '">Click here to read more</a>';
        Subscribes::chunk(100, function ($subcribes) use ($content){
            foreach ($subcribes as $subcribe) {
                $job = new SendMailJob($subcribe->email, 'New Blog', $content);
                dispatch($job);
            }
        });
    }
}
