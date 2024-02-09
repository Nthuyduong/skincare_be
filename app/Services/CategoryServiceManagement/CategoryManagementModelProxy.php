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

        if (isset($filter['search'])) {
            $query = $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $filter['search'] . '%');
            });
        }
        if (isset($filter['has_parent'])) {
            if ($filter['has_parent'] == 'true') {
                $query = $query->whereNotNull('parent_id');
            } else {
                $query = $query->whereNull('parent_id');
            }
        }

        $count = $query->count();

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
        $category->status = $data['status'];
        $category->save();
        return $category;
    }

    function getCategoryById($id)
    {
        return Category::where('id', $id)
        ->with('childrens')
        ->with('parent')
        ->withCount('blogs')
        ->first();
    }

    function updateCategory($id, $data)
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            return null;
        }

        $category->name = $data['name'] ?? $category->name;
        $category->description = $data['description'] ?? $category->description;
        $category->feature_img = $data['feature_img'] ?? $category->feature_img;
        $category->status = $data['status'] ?? $category->status;

        if (isset($data['parent_id'])) {
            $category->parent_id = $data['parent_id'];
        }
        $category->save();

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

    function getCategoriesByParentId($id, $page = 1, $limit)
    {
        $query = Category::query();

        // select * from categories where parent_id = $id
        $query = $query->where('parent_id', $id);

        $count = $query->count();

        // select * from categories where parent_id = $id limit $limit offset ($page - 1) * $limit
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

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
}

