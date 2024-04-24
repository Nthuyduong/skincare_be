<?php

namespace App\Services\ContactServiceManagement;

use App\Jobs\SendMailJob;
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
        $job = new SendMailJob($contact->email, $setting->title, $setting->content);
        dispatch($job);
        return $contact;
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