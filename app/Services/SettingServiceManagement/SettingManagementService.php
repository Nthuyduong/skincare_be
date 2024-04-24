<?php

namespace App\Services\SettingServiceManagement;

use App\Jobs\SendMailJob;
use App\Models\Blog;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Log;

class SettingManagementService
{
    private $modelProxy;

    public function __construct(SettingManagementModelProxy $modelProxy)
    {
        $this->modelProxy = $modelProxy;
    }

    public function getSetting($type)
    {
        return $this->modelProxy->getSetting($type);
    }

    public function updateSetting($data)
    {
        return $this->modelProxy->updateSetting($data);
    }

    public function testSettingMail($type, $email)
    {
        if ($type == MailSetting::TYPE_CONTACT) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_CONTACT);
            $job = new SendMailJob($email, $setting->title, $setting->content);
            dispatch($job);
        } elseif ($type == MailSetting::TYPE_SUBSCRIBE) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_SUBSCRIBE);
            $job = new SendMailJob($email, $setting->title, $setting->content);
            dispatch($job);
        } elseif ($type == MailSetting::TYPE_NOTIFICATION) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_NOTIFICATION);
            $blog = Blog::whereNotNull('publish_date')->orderBy('publish_date', 'desc')->first();
            $content = $setting->content;

            $content = str_replace('[[title]]', $blog->title, $content);
            $content = str_replace('[[summary]]', $blog->content, $content);
            $content = str_replace('[[link]]', config('app.fe_url') . "/article/" . $blog->slug, $content);
            $content = str_replace('[[image]]', config('app.url') . "/storage/desktop/" . $blog->featured_img, $content);
            $content = str_replace('[[banner]]', config('app.url') . "/storage/desktop/" . $blog->banner_img, $content);

            $job = new SendMailJob($email, $setting->title, $content);
            dispatch($job);
        }
    }
}