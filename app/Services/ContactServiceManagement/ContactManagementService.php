<?php

namespace App\Services\ContactServiceManagement;

use App\Helpers\MailHelper;
use App\Jobs\SendMailJob;
use App\Jobs\SendMailPortfolioJob;
use App\Models\MailSetting;
use App\Services\SettingServiceManagement\SettingManagementModelProxy;

class ContactManagementService
{
    protected $contactManagementProxy;
    protected $settingManagementProxy;

    public function __construct(ContactManagementProxy $contactManagementProxy, SettingManagementModelProxy $settingManagementProxy)
    {
        $this->contactManagementProxy = $contactManagementProxy;
        $this->settingManagementProxy = $settingManagementProxy;
    }

    public function createContact($data)
    {
        $contact = $this->contactManagementProxy->createContact($data);
        $setting = $this->settingManagementProxy->getSetting(MailSetting::TYPE_CONTACT);
        
        $content = $setting->content;
        $content = str_replace('[[name]]', $contact->name, $content);
        $content = str_replace('[[now]]', date('d M Y'), $content);
        $job = new SendMailJob($contact->email, $setting->title, $content);
        dispatch($job);
        return $contact;
    }

    public function createContactPortfolio($data) {
        $setting = $this->settingManagementProxy->getSetting(MailSetting::TYPE_PORTFOLIO);
        $content = $setting->content;
        $content = str_replace('[[name]]', $data['name'], $content);
        $content = str_replace('[[now]]', date('d M Y'), $content);
        $job = new SendMailPortfolioJob($data['email'], $setting->title, $setting->content);
        dispatch($job);
    }

    public function getContact($id)
    {
        return $this->contactManagementProxy->getContact($id);
    }

    public function getAllContactWithFilter($page = 1, $limit = 10, $filter = [])
    {
        return $this->contactManagementProxy->getAllContactWithFilter($page, $limit, $filter);
    }

    public function deleteContact($id)
    {
        return $this->contactManagementProxy->deleteContact($id);
    }
}