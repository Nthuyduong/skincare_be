<?php

namespace App\Jobs;

use App\Helpers\MailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $content;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $subject, $content)
    {
        $this->queue = 'SendMail';
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        MailHelper::sendMailCc(
            config('mail.from.address'),
            config('mail.from.name'),
            '',
            '',
            $this->email,
            [],
            $this->subject,
            ['template' => 'emails.contact', 'content' => $this->content],
            []
        );
    }
}
