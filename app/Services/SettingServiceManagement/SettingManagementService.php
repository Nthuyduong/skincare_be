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
            $content = $setting->content;
            $content = str_replace('[[name]]', 'Duong', $content);
            $content = str_replace('[[now]]', date('d M Y'), $content);
            $job = new SendMailJob($email, $setting->title, $content);
            dispatch($job);
        } elseif ($type == MailSetting::TYPE_SUBSCRIBE) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_SUBSCRIBE);
            $content = $setting->content;
            $content = str_replace('[[name]]', 'Duong', $content);
            $content = str_replace('[[now]]', date('d M Y'), $content);
            $job = new SendMailJob($email, $setting->title, $content);
            
            dispatch($job);
        } elseif ($type == MailSetting::TYPE_NOTIFICATION) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_NOTIFICATION);
            $blog = Blog::whereNotNull('publish_date')
                ->with('categories')
                ->orderBy('publish_date', 'desc')
                ->first();
            $content = $setting->content;

            $content = str_replace('[[name]]', 'Duong', $content);
            $content = str_replace('[[now]]', date('d M Y'), $content);

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

            $job = new SendMailJob($email, $setting->title, $content);
            dispatch($job);
        } elseif ($type == MailSetting::TYPE_PORTFOLIO) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_PORTFOLIO);
            $content = $setting->content;
            $content = str_replace('[[name]]', 'Duong', $content);
            $content = str_replace('[[now]]', date('d M Y'), $content);
            $job = new SendMailJob($email, $setting->title, $content);
            dispatch($job);
        } elseif ($type == MailSetting::TYPE_REPLY) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_REPLY);
            $content = $setting->content;
            $content = str_replace('[[name]]', 'Duong', $content);
            $content = str_replace('[[now]]', date('d M Y'), $content);
            $job = new SendMailJob($email, $setting->title, $content);
            dispatch($job);
        } elseif ($type == MailSetting::TYPE_REPLY) {
            $setting = $this->modelProxy->getSetting(MailSetting::TYPE_REPLY);
            $content = $setting->content;
            $content = str_replace('[[name]]', 'Duong', $content);
            $content = str_replace('[[now]]', date('d M Y'), $content);
            $job = new SendMailJob($email, $setting->title, $content);
            dispatch($job);
        }
    }
}