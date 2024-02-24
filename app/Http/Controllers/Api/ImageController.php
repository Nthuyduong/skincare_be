<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\ImageServiceManagement\ImageServiceManagementService;
use Illuminate\Http\Request;
use Exception;

class ImageController extends ApiController
{
    protected $imageManagementService;

    public function __construct(ImageServiceManagementService $imageManagementService)
    {
        $this->imageManagementService = $imageManagementService;
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
            $filter['sort'] = $request->input('sort');

            $images = $this->imageManagementService->getAllWithFilter($page, $limit, $filter);

            return response()->json([
                'data' => $images,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $data = [];
            $data['files'] = $request->file('files');
            $data['suggest'] = $request->input('suggest');
            $data['alt'] = $request->input('alt');

            $image = $this->imageManagementService->uploadImage($data);
            return response()->json([
                'data' => $image,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function getImageById(string $id)
    {
        try {
            $image = $this->imageManagementService->getImageById($id);
            return response()->json([
                'data' => $image,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function updateImage(Request $request)
    {
        try {
            $data = [];
            $ids = $request->input('ids');
            $data['alt'] = $request->input('alt');
            $data['suggest'] = $request->input('suggest');

            $image = $this->imageManagementService->updateImage($ids, $data);
            return response()->json([
                'data' => $image,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function deleteImage(Request $request)
    {
        try {
            $ids = $request->input('ids');
            $image = $this->imageManagementService->deleteImage($ids);
            return response()->json([
                'data' => $image,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
}