<?php

namespace App\Services\CategoryServiceManagement;

use App\Exceptions\SlugExistException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class CategoryManagementService
{

    protected $CategoryManagementModelProxy;

    public function __construct(CategoryManagementModelProxy $categoryManagementModelProxy)
    {
        $this->CategoryManagementModelProxy = $categoryManagementModelProxy;
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {

    }

    function createCategory($data)
    {

    }

    // bài tập về nhà tạo 1 api để update blog
}
