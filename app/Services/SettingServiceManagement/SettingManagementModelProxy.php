<?php

namespace App\Services\SettingServiceManagement;

use App\Models\MailSetting;

class SettingManagementModelProxy
{
    function getSetting($type)
    {
        return MailSetting::where('type', $type)->first();
    }

    function updateSetting($data)
    {
        $setting = MailSetting::where('type', $data['type'])->first();
        if ($setting) {
            $setting->title = $data['title'];
            $setting->content = $data['content'];
            $setting->save();
            return $setting;
        }
        return $setting;
    }
}