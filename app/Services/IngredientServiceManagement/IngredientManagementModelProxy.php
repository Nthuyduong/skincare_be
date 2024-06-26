<?php

namespace App\Services\IngredientServiceManagement;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientDetail;
use Illuminate\Support\Facades\Log;
use DateTime;

class IngredientManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Ingredient::with('details');

        $count = $query->count();

        if (isset($filter['search'])) {
            $query = $query->where('title', 'like', '%' . $filter['search'] . '%');
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

    function checkByNames($names)
    {
        return Ingredient::where('name', $names)->first();
    }

    function createIngredient($data)
    {
        $ingredient = new Ingredient();
        $ingredient->name = $data['name'];
        $ingredient->description = $data['description'];
        $ingredient->featured_img = $data['featured_img'];
        $ingredient->featured_img2 = $data['featured_img2'];
        $ingredient->content = $data['content'];
        $ingredient->suggest = $data['suggest'];
        $ingredient->slug = $data['slug'];
        $ingredient->meta_title = $data['meta_title'];
        $ingredient->meta_description = $data['meta_description'];
        $ingredient->save();

        if (isset($data['details'])) {
            foreach ($data['details'] as $detail) {
                $newDetail = new IngredientDetail;
                $newDetail->ingredient_id = $ingredient->id;
                $newDetail->name = $detail['name'];
                $newDetail->content = $detail['content'];
                $newDetail->save();
            }
        }
        return $ingredient;
    }

    function getIngredientById($id)
    {
        return Ingredient::where('id', $id)
            ->with('details')
            ->first();
    }

    function getIngredientBySlug($slug) {
        // select * from ingredients where slug = $slug
        return Ingredient::where('slug', $slug)
            ->with('details')
            ->first();
    }

    function updateIngredient($id, $data)
    {
        $ingredient = $this->getIngredientById($id);

        if (!$ingredient) {
            return null;
        }

        $ingredient->name = $data['name'] ?? $ingredient->name;
        $ingredient->description = $data['description'] ?? $ingredient->description;
        $ingredient->featured_img = $data['featured_img'] ?? $ingredient->featured_img;
        $ingredient->featured_img2 = $data['featured_img2'] ?? $ingredient->featured_img2;
        $ingredient->content = $data['content'] ?? $ingredient->content;
        $ingredient->suggest = $data['suggest'] ?? $ingredient->suggest;
        $ingredient->slug = $data['slug'] ?? $ingredient->slug;
        $ingredient->meta_title = $data['meta_title'] ?? $ingredient->meta_title;
        $ingredient->meta_description = $data['meta_description'] ?? $ingredient->meta_description;
       
        if (isset($data['details'])) {
            $ingredient->details()->forceDelete();
            foreach ($data['details'] as $detail) {
                $newDetail = new IngredientDetail;
                $newDetail->ingredient_id = $ingredient->id;
                $newDetail->name = $detail['name'];
                $newDetail->content = $detail['content'];
                $newDetail->save();
            }
        }
        $ingredient->save();
        return $ingredient;
    }

    function deleteIngredient($id)
    {
        $ingredient = $this->getIngredientById($id);
        $ingredient->details()->forceDelete();
        $ingredient->delete();
        return $ingredient;
    }

    function getAllWithoutPagination()
    {
        return Ingredient::all();
    }
}

