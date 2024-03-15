<?php

namespace App\Console\Commands;

use App\Jobs\SendMailNotication;
use Illuminate\Console\Command;

class TestSendMailNotication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-send-mail-notication {--email=}';

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
        $job = new SendMailNotication($email, 'Test send mail notication', 'This is a test email notication');
        dispatch($job);
    }
}
