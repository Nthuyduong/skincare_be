<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\ApiController;
use App\Models\Test;
use App\Services\TestManagement\TestManagementService;
use Illuminate\Http\Request;

class TestController extends ApiController
{


    public function __construct()
    {
    }

    public function createTest(Request $request){
        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'msg' => 'test created',
        ]);
    }

    public function test()
    {
        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'msg' => 'test'
        ]);
    }
}
