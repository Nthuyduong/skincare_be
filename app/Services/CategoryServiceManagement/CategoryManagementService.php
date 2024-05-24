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
        if (isset($data['featured_img'])) {
            $featured_img = ImageHelper::resizeImage($data['featured_img']);
            $data['featured_img'] = $featured_img['original'];
        }
        if (isset($data['banner_img'])) {
            $banner_img = ImageHelper::resizeImage($data['banner_img']);
            $data['banner_img'] = $banner_img['original'];
        }
        return $this->CategoryManagementModelProxy->createCategory($data);
    }

    function updateCategory($id, $data)
    {
        $category = $this->CategoryManagementModelProxy->findCategoryById($id);
        if (isset($data['featured_img'])) {
            $featured_img = ImageHelper::resizeImage($data['featured_img']);
            $data['featured_img'] = $featured_img['original'];
        }
        if (isset($data['banner_img'])) {
            $banner_img = ImageHelper::resizeImage($data['banner_img']);
            $data['banner_img'] = $banner_img['original'];
        }
        $updateCategory = $this->CategoryManagementModelProxy->updateCategory($id, $data);

        if (isset($data['featured_img']) && $updateCategory) {
            ImageHelper::removeImage($category->featured_img);
        }
        if (isset($data['banner_img']) && $category->banner_img) {
            ImageHelper::removeImage($category->banner_img);
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

    function deleteCategory($id)
    {
        $category = $this->CategoryManagementModelProxy->findCategoryById($id);
        if ($category) {
            if ($category->featured_img) {
                ImageHelper::removeImage($category->featured_img);
            }
            if ($category->banner_img) {
                ImageHelper::removeImage($category->banner_img);
            }
            return $this->CategoryManagementModelProxy->deleteCategory($id);
        }
        return false;
    }

    function getCategoryBySlug(string $slug)
    {
        return $this->CategoryManagementModelProxy->getCategoryBySlug($slug);
    }

    function getCategoryByParentSlug(string $slug)
    {
        return $this->CategoryManagementModelProxy->getCategoryByParentSlug($slug);
    }
}
