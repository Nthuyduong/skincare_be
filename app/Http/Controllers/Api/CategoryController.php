<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use App\Services\CategoryServiceManagement\CategoryManagementService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\ValidationException;

class CategoryController extends ApiController
{
    protected $categoryManagementService;

    public function __construct(CategoryManagementService $categoryManagementService)
    {
        $this->categoryManagementService = $categoryManagementService;
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
            $filter['has_parent'] = $request->input('has_parent');

            $category = $this->categoryManagementService->getAllWithFilter($page, $limit, $filter);
            return response()->json([
                'data' => $category,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function createCategory(Request $request)
    {
        try {

            $this->validate($request, [
                'name' => 'required',
                'slug' => 'required',
            ]);
            $data = [];
            $data['name'] = $request->input('name');
            $data['description'] = $request->input('description');
            $data['featured_img'] = $request->file('featured_img');
            $data['banner_img'] = $request->file('banner_img');
            $data['status'] = $request->input('status');
            $data['meta_title'] = $request->input('meta_title');
            $data['meta_description'] = $request->input('meta_description');
            $data['slug'] = $request->input('slug');
            $data['parent_id'] = $request->input('parent_id');

            $category = $this->categoryManagementService->createCategory($data);

            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => 'test created',
                'data' => $category,
            ]);
        } catch (ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function updateCategory(Request $request, string $id)
    {
        try {

            $this->validate($request, [
                'name' => 'required|string|max:255',
                'slug' => 'required',
            ]);
            $data = [];
            $data['name'] = $request->input('name');
            $data['description'] = $request->input('description');
            $data['featured_img'] = $request->file('featured_img');
            $data['banner_img'] = $request->file('banner_img');
            $data['status'] = $request->input('status');
            $data['meta_title'] = $request->input('meta_title');
            $data['meta_description'] = $request->input('meta_description');
            $data['slug'] = $request->input('slug');
            $data['parent_id'] = $request->input('parent_id');

            $categories = $this->categoryManagementService->updateCategory($id, $data);

            return response()->json([
                'data' => $categories,
                'status' => self::STATUS_SUCCESS,
                'msg' => $id,
            ]);
        } catch (ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function getCategoryById(string $id)
    {
        try {

            $category = $this->categoryManagementService->getCategoryById($id);

            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => $id,
                'data' => $category
            ]);
        } catch (ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function getCategoriesByParentId(string $id)
    {
        try {

            $categories = $this->categoryManagementService->getCategoriesByParentId($id);

            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => $id,
                'data' => $categories
            ]);
        } catch (ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
}
