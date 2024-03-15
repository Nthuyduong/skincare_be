<?php

namespace App\Console\Commands;

use App\Jobs\SendMailJob;
use Illuminate\Console\Command;

class TestSendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-send-mail {--email=}';

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
        $email = $this->option('email');

        if (empty($email)) {
            $this->error('Email is required');
            return;
        }
        $job = new SendMailJob($email, 'Test send mail', 'This is a test email');
        dispatch($job);
        $this->info('Send mail success: with ' . $email);
    }
}
