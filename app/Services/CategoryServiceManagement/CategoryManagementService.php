<?php

namespace App\Services\CategoryServiceManagement;

use App\Exceptions\SlugExistException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageHelper;

class CategoryManagementService
{

    protected $CategoryManagementModelProxy;

    public function __construct(CategoryManagementModelProxy $categoryManagementModelProxy)
    {
        $this->CategoryManagementModelProxy = $categoryManagementModelProxy;
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $categories = $this->CategoryManagementModelProxy->getAllWithFilter($page, $limit, $filter);
        return $categories;
    }

    function createCategory($data)
    {
        if (isset($data['feature_img'])) {
            $feature_img = ImageHelper::resizeImage($data['feature_img']);
            $data['feature_img'] = $feature_img['original'];
        }
        return $this->CategoryManagementModelProxy->createCategory($data);
    }

    function updateCategory($id, $data)
    {
        $category = $this->CategoryManagementModelProxy->findCategoryById($id);
        if (isset($data['feature_img'])) {
            $feature_img = ImageHelper::resizeImage($data['feature_img']);
            $data['feature_img'] = $feature_img['original'];
        }
        $updateCategory = $this->CategoryManagementModelProxy->updateCategory($id, $data);

        if (isset($data['feature_img']) && $updateCategory) {
            ImageHelper::removeImage($category->feature_img);
        }

        return $updateCategory;
    }
    function getCategoryById($id)
    {
        return $this->CategoryManagementModelProxy->getCategoryById($id);
    }

    function getCategoriesByParentId($id, $page = 1, $limit = 10)
    {
        return $this->CategoryManagementModelProxy->getCategoriesByParentId($id, $page, $limit);
    }
}
