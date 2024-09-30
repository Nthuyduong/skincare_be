<?php

namespace App\Services\CategoryServiceManagement;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Models\CategoryTran;
use App\Helpers\LocaleHelper;

class CategoryManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $locale = app()->getLocale();
        $query = Category::query();

        if ($locale &&  $locale != 'en') {
            $query = $query->with($locale)
                ->with('childrens.' . $locale)
                ->with('parent.' . $locale);
        } else {
            $query->with('childrens')
                ->with('parent');
        }

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
            ->get()
            ->toArray();
        // select * from blogs
        return [
            'results' => LocaleHelper::convertCategoriesToLocale($results, $locale ?? 'en'),
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
        $category->featured_img = $data['featured_img'];
        $category->banner_img = $data['banner_img'];
        $category->status = $data['status'];
        if (isset($data['parent_id'])) {
            $category->parent_id = $data['parent_id'];
        }
        $category->slug = $data['slug'];
        $category->meta_title = $data['meta_title'];
        $category->meta_description = $data['meta_description'];
        $category->save();
        return $category;
    }

    function getCategoryById($id, $isArray = false)
    {
        $locale = app()->getLocale();
        $query = Category::where('id', $id);

        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('childrens.' . $locale)
                ->with('parent.' . $locale);
        } else {
            $query = $query->with('childrens')
                ->with('parent');
        }
        $query = $query->withCount('blogs');

        if (!$isArray) {
            return $query->first();
        }

        return LocaleHelper::convertCategoryToLocale($query->first()->toArray(), $locale);
    }

    function findCategoryById($id)
    {
        return Category::where('id', $id)->first();
    }

    function updateCategory($id, $data)
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            return null;
        }
        $category->name = $data['name'] ?? $category->name;
        $category->description = $data['description'] ?? $category->description;
        $category->featured_img = $data['featured_img'] ?? $category->featured_img;
        $category->banner_img = $data['banner_img'] ?? $category->banner_img;
        $category->status = $data['status'] ?? $category->status;
        $category->slug = $data['slug'] ?? $category->slug;
        $category->meta_title = $data['meta_title'] ?? $category->meta_title;
        $category->meta_description = $data['meta_description'] ?? $category->meta_description;
        $category->parent_id = $data['parent_id'] ?? $category->parent_id;
        
        $category->save();

        return $category;
    }

    function createOrUpdateCategoryTran($id, $data)
    {
        $locale = app()->getLocale();
        $categoryTran = CategoryTran::where('category_id', $id)
            ->where('locale', $locale)
            ->first();
        if (!$categoryTran) {
            $categoryTran = new CategoryTran();
            $categoryTran->category_id = $id;
            $categoryTran->locale = $locale;
        }
        $categoryTran->name = $data['name'] ?? $categoryTran->name;
        $categoryTran->slug = $data['slug'] ?? $categoryTran->slug;
        $categoryTran->meta_title = $data['meta_title'] ?? $categoryTran->meta_title;
        $categoryTran->meta_description = $data['meta_description'] ?? $categoryTran->meta_description;
        $categoryTran->description = $data['description'] ?? $categoryTran->description;
        $categoryTran->save();

        return $categoryTran;
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
        $locale = app()->getLocale();
        $query = Category::query();

        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('childrens.' . $locale)
                ->with('parent.' . $locale);
        } else {
            $query = $query->with('childrens')
                ->with('parent');
        }

        // select * from categories where parent_id = $id
        $query = $query->where('parent_id', $id);

        $count = $query->count();

        // select * from categories where parent_id = $id limit $limit offset ($page - 1) * $limit
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return [
            'results' => LocaleHelper::convertCategoriesToLocale($results->toArray(), $locale),
            'paginate' => [
                'current' => $page,
                'limit' => $limit,
                'last' => ceil($count / $limit),
                'count' => $count,
            ]
        ];
    }

    function deleteCategory($id)
    {
        $category = $this->findCategoryById($id);
        if ($category) {
            $category->blogs()->detach();
            $category->delete();
            return $category;
        }
        return null;
    }

    function getCategoryBySlug($slug)
    {
        $locale = app()->getLocale();
        $query = Category::where('slug', $slug)
            ->orWhereHas('locales', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });

        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('childrens.' . $locale)
                ->with('parent.' . $locale);
        } else {
            $query = $query->with('childrens')
                ->with('parent');
        }

        $query = $query->withCount('blogs')
            ->first();
        if (!$query) {
            return null;
        }

        return LocaleHelper::convertCategoryToLocale($query->toArray(), $locale);
    }

    function getCategoryByParentSlug($slug, $page = 1, $limit = 10)
    {
        $locale = app()->getLocale();
        $query = Category::query();

        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('childrens.' . $locale)
                ->with('parent.' . $locale);
        } else {
            $query = $query->with('childrens')
                ->with('parent');
        }

        $parent = Category::where('slug', $slug)
            ->orWhereHas('locales', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->first();

        $query = $query->where('parent_id', $parent->id);

        $count = $query->count();
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return [
            'results' => LocaleHelper::convertCategoriesToLocale($results->toArray(), $locale),
            'paginate' => [
                'current' => $page,
                'limit' => $limit,
                'last' => ceil($count / $limit),
                'count' => $count,
            ]
        ];
    }
}

