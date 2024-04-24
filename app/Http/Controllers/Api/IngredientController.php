<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExceptionMessage;
use App\Http\Controllers\ApiController;
use App\Models\Category;
use App\Services\IngredientServiceManagement\IngredientManagementService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class IngredientController extends ApiController
{
    protected $ingredientManagementService;

    public function __construct(IngredientManagementService $ingredientManagementService)
    {
        $this->ingredientManagementService = $ingredientManagementService;
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

            $ingredients = $this->ingredientManagementService->getAllWithFilter($page, $limit, $filter);
            return response()->json([
                'data' => $ingredients,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function createIngredient(Request $request)
    {
        try {

            $this->validate($request, [
                'name' => 'required',
            ]);
            $data = [];
            $data['name'] = $request->input('name');
            $data['description'] = $request->input('descripion');
            $data['featured_img'] = $request->file('featured_img');
            $data['featured_img2'] = $request->file('featured_img2');
            $data['content'] = $request->input('content');
            $data['suggest'] = $request->input('suggest');
            // details is an array of objects json
            $data['details'] = json_decode($request->input('details'), true);

            $ingredients = $this->ingredientManagementService->createIngredient($data);

            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => 'test created',
                'data' => $ingredients,
            ]);
        } catch (ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }  catch(ExceptionMessage $e) {
            return $this->clientErrorResponse($e->getMessage(), 500);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function updateIngredient(Request $request, string $id)
    {
        try {
            $data = [];
            $data['name'] = $request->input('name');
            $data['description'] = $request->input('descripion');
            $data['featured_img'] = $request->file('featured_img');
            $data['featured_img2'] = $request->file('featured_img2');
            $data['content'] = $request->input('content');
            $data['suggest'] = $request->input('suggest');
            $data['details'] = json_decode($request->input('details'), true);

            $ingredients = $this->ingredientManagementService->updateIngredient($id, $data);

            return response()->json([
                'data' => $ingredients,
                'status' => self::STATUS_SUCCESS,
                'msg' => $id,
            ]);
        } catch (ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function deleteIngredient($id)
    {
        try {
            $ingredients = $this->ingredientManagementService->deleteIngredient($id);

            return response()->json([
                'data' => $ingredients,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);

        } catch (ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function getIngredientById(string $id)
    {
        try {
            $ingredient = $this->ingredientManagementService->getIngredientById($id);
            return response()->json([
                'data' => $ingredient,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
    public function getAllWithoutPagination() {
        try {
            $ingredients = $this->ingredientManagementService->getAllWithoutPagination();
            return response()->json([
                'data' => $ingredients,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
}
