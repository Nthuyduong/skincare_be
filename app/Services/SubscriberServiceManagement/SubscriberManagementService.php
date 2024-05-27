<?php

namespace App\Services\SubscriberServiceManagement;
use App\Jobs\SendMailJob;
use App\Exceptions\ExceptionMessage;
use App\Models\MailSetting;
use App\Services\SettingServiceManagement\SettingManagementModelProxy;

class SubscriberManagementService
{
    protected $subscriberManagementProxy;
    protected $settingManagementProxy;

    public function __construct(SubscriberManagementModelProxy $subscriberManagementProxy, SettingManagementModelProxy $settingManagementProxy)
    {
        $this->subscriberManagementProxy = $subscriberManagementProxy;
        $this->settingManagementProxy = $settingManagementProxy;
    }

    public function subscribe($data)
    {
        $check = $this->subscriberManagementProxy->getSubscribeByEmail($data['email']);
        if ($check) {
            throw new ExceptionMessage('Email already subscribed');
        }
        $sub = $this->subscriberManagementProxy->createSubscribe($data);
        $setting = $this->settingManagementProxy->getSetting(MailSetting::TYPE_SUBSCRIBE);
        
        $content = $setting->content;
        $content = str_replace('[[name]]', $sub->name, $content);
        $content = str_replace('[[now]]', date('d M Y'), $content);
        $job = new SendMailJob($sub->email, $setting->title, $content);
        dispatch($job);
        return $sub;
    }

    public function getSubscribe($id)
    {
        return $this->subscriberManagementProxy->getSubscribe($id);
    }

    public function getAllSubscribeWithFilter($page = 1, $limit = 10, $filter = [])
    {
        return $this->subscriberManagementProxy->getAllSubscribeWithFilter($page, $limit, $filter);
    }

    public function deleteSubscribe($id)
    {
        return $this->subscriberManagementProxy->deleteSubscribe($id);
    }
}