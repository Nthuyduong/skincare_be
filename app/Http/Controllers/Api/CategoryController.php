<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use App\Services\CategoryServiceManagement\CategoryManagementService;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{

    protected $categoryManagementService;

    public function __construct(CategoryManagementService $categoryManagementService)
    {
        $this->categoryManagementService = $categoryManagementService;
    }


    public function getAll(Request $request)
    {

    }

    public function createCategory(Request $request)
    {

    }

    public function updateCategory(Request $request)
    {

    }

}
