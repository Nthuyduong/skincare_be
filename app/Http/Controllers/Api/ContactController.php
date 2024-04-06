<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\ApiController;

use App\Services\ContactServiceManagement\ContactManagementService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ContactController extends ApiController
{

    protected $contactService;

    public function __construct(ContactManagementService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function createContact(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required',
                'message' => 'required',
            ]);

            $data = [];

            $data['name'] = $request->input('name');
            $data['email'] = $request->input('email');
            $data['message'] = $request->input('message');

            $contact = $this->contactService->createContact($data);

            return response()->json([
                'data' => $contact,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);

        } catch (ValidationException $e) {
            return $this->validationErrorResponse(__METHOD__, $e);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function getContactById($id)
    {
        try {
            $contact = $this->contactService->getContact($id);
            return response()->json([
                'data' => $contact,
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

            $contact = $this->contactService->getAllContactWithFilter($page, $limit, $filter);
            return response()->json([
                'data' => $contact,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function deleteContact($id)
    {
        try {
            $this->contactService->deleteContact($id);
            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
}