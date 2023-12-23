<?php

namespace App\Services\CategoryServiceManagement;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use DateTime;

class CategoryManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Category::query();

        $query = $query->with('childrens')->with('parent');

        $count = $query->count();

        if (isset($filter['search'])) {
            $query = $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $filter['search'] . '%');
            });
        }

        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
        // select * from blogs
        return [
            'results' => $results,
            'paginate' => [
                'current' => $page,
                'limit' => $limit,
                'last' => ceil($count / $limit),
                'count' => $count,
            ]
        ];
    }

    function createCategory($data)
    {
        $category = new Category();
        $category->name = $data['name'];
        $category->description = $data['description'];
        $category->feature_img = $data['feature_img'];
        $category->save();
        return $category;
    }

    function getCategoryById($id)
    {
        $category = Category::find($id);
        return $category;
    }

    function updateCategory($id, $data)
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            return null;
        }

        $updateFields = ['name', 'description', 'feature_img'];
        $category->update(array_only($data, $updateFields));

        return $category;
    }

    function updateCategoryStatus($id, $status)
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            return null;
        }

        $category->status = $status;
        $category->save();

        return $category;
    }
}

