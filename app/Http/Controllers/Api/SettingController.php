<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\SettingServiceManagement\SettingManagementService;
use Illuminate\Http\Request;

class SettingController extends ApiController
{
    private $settingManagementService;

    public function __construct(SettingManagementService $settingManagementService)
    {
        $this->settingManagementService = $settingManagementService;
    }

    public function getSettingMail($type)
    {
        try {
            $setting = $this->settingManagementService->getSetting($type);
            return response()->json([
                'status' => 'success',
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function updateSettingMail($type, Request $request)
    {
        try {
            $data = [];
            $data['type'] = $type;
            $data['title'] = $request->input('title');
            $data['content'] = $request->input('content');
            $setting = $this->settingManagementService->updateSetting($data);
            return response()->json([
                'status' => 'success',
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
    public function testSettingMail($type, Request $request)
    {
        try {
            $email = $request->input('email');
            $this->settingManagementService->testSettingMail($type, $email);
            return response()->json([
                'status' => 'success',
                'data' => 'success'
            ]);
        } catch (\Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
}