<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class CreateAdminAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // run command: php artisan create:admin --
    protected $signature = 'create:admin {--email=} {--password=}';

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
        $password = $this->option('password');

        if (empty($email) || empty($password)) {
            $this->error('Email and password are required');
            return;
        }
        $admin = new Admin();
        $admin->email = $email;
        $admin->password = bcrypt($password);
        $admin->save();

        $this->info('Create admin success: with ' . $email . ' ' . $password);
    }
}
