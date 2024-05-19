<?php

namespace App\Jobs;

use App\Helpers\MailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailPortfolioJob implements ShouldQueue
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
        $this->queue = 'SendMailPortfolio';
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        MailHelper::sendMailPortfolioCc(
            config('mail.from.portfolio_address'),
            config('mail.from.portfolio_name'),
            '',
            '',
            $this->email,
            [],
            $this->subject,
            ['template' => 'emails.contact-portfolio', 'content' => $this->content],
            []
        );
    }
}