<?php

namespace App\Services\IngredientServiceManagement;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use DateTime;

class IngredientManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Ingredient::query();

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

    function createIngredient($data)
    {
        $ingredient = new Ingredient();
        $ingredient->name = $data['name'];
        $ingredient->description = $data['description'];
        $ingredient->feature_img = $data['feature_img'];
        $ingredient->status = $data['status'];
        $ingredient->save();
        return $ingredient;
    }

    function getIngredientById($id)
    {
        return Ingredient::where('id', $id)->first();
    }

    function updateIngredient($id, $data)
    {
        $ingredient = $this->getIngredientById($id);

        if (!$ingredient) {
            return null;
        }

        $ingredient->name = $data['name'] ?? $ingredient->name;
        $ingredient->description = $data['description'] ?? $ingredient->description;
        $ingredient->feature_img = $data['feature_img'] ?? $ingredient->feature_img;
        $ingredient->status = $data['status'] ?? $ingredient->status;

        return $ingredient;
    }

    function updateIngredientStatus($id, $status)
    {
        $ingredient = $this->getIngredientById($id);

        if (!$ingredient) {
            return null;
        }

        $ingredient->status = $status;
        $ingredient->save();

        return $ingredient;
    }
}

