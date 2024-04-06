<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExceptionMessage;
use App\Http\Controllers\ApiController;
use App\Services\SubscriberServiceManagement\SubscriberManagementService;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubscriberController extends ApiController
{
    
    protected $subscriberManagementService;

    public function __construct(SubscriberManagementService $subscriberManagementService)
    {
        $this->subscriberManagementService = $subscriberManagementService;
    }

    public function subscribe(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required',
            ]);

            $data = [];

            $data['email'] = $request->input('email');

            $subscriber = $this->subscriberManagementService->subscribe($data);

            return response()->json([
                'data' => $subscriber,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);

        } catch (ValidationException $e) {
            return $this->validationErrorResponse(__METHOD__, $e);
        } catch(ExceptionMessage $e) {
            return $this->clientErrorResponse($e->getMessage(), 500);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function getSubscribeById($id)
    {
        try {
            $subscriber = $this->subscriberManagementService->getSubscribe($id);
            return response()->json([
                'data' => $subscriber,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function getAll(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            if ($page < 1) {
                $page = 1;
            }
            $limit = $request->input('limit', 10);
            if ($limit < 1) {
                $limit = 10;
            }
            $filter = [];
            $filter['search'] = $request->input('search');
            $filter['status'] = $request->input('status');

            $subscriber = $this->subscriberManagementService->getAllSubscribeWithFilter($page, $limit, $filter);
            return response()->json([
                'data' => $subscriber,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function delete($id)
    {
        try {
            $this->subscriberManagementService->deleteSubscribe($id);
            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
}