<?php

namespace App\Services\SettingServiceManagement;

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
}