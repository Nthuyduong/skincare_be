<?php

namespace App\Console\Commands;

use App\Models\MailSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MigrateMailSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:mail-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $mailSettings = [
            [
                'type' => MailSetting::TYPE_SUBSCRIBE,
                'title' => 'Subscribe',
                'content' => 'This is a subscribe email',
            ],
            [
                'type' => MailSetting::TYPE_CONTACT,
                'title' => 'Contact',
                'content' => 'This is a contact email',
            ],
            [
                'type' => MailSetting::TYPE_NOTIFICATION,
                'title' => 'Notification',
                'content' => 'This is a notification email',
            ],
        ];

        foreach ($mailSettings as $mailSetting) {
            $mailSettingModel = new MailSetting();
            $mailSettingModel->type = $mailSetting['type'];
            $mailSettingModel->title = $mailSetting['title'];
            $mailSettingModel->content = $mailSetting['content'];
            $mailSettingModel->save();
        }

        $this->info('Migrate mail settings success');
    }
}