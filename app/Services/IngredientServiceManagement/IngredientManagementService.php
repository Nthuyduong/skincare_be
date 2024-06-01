<?php

namespace App\Services\IngredientServiceManagement;

use App\Exceptions\ExceptionMessage;
use App\Exceptions\SlugExistException;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class IngredientManagementService
{

    protected $IngredientManagementModelProxy;

    public function __construct(IngredientManagementModelProxy $ingredientManagementModelProxy)
    {
        $this->IngredientManagementModelProxy = $ingredientManagementModelProxy;
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        return $this->IngredientManagementModelProxy->getAllWithFilter($page, $limit, $filter);
    }

    function createIngredient($data)
    {
        $check = $this->IngredientManagementModelProxy->checkByNames($data['name']);
        if ($check) {
            throw new ExceptionMessage('Ingredient name already exist');
        }
        if (isset($data['featured_img'])) {
            $featured_img = ImageHelper::resizeImage($data['featured_img']);
            $data['featured_img'] = $featured_img['original'];
        }
        if (isset($data['featured_img2'])) {
            $featured_img2 = ImageHelper::resizeImage($data['featured_img2']);
            $data['featured_img2'] = $featured_img2['original'];
        }
        return $this->IngredientManagementModelProxy->createIngredient($data);
    }

    function updateIngredient($id, $data)
    {
        $ingredient = $this->IngredientManagementModelProxy->getIngredientById($id);
        if (isset($data['featured_img'])) {
            $featured_img = ImageHelper::resizeImage($data['featured_img']);
            $data['featured_img'] = $featured_img['original'];
        }
        if (isset($data['featured_img2'])) {
            $featured_img2 = ImageHelper::resizeImage($data['featured_img2']);
            $data['featured_img2'] = $featured_img2['original'];
        }
        $updated = $this->IngredientManagementModelProxy->updateIngredient($id, $data);

        if (isset($data['featured_img']) && $updated) {
            ImageHelper::removeImage($ingredient->featured_img);
        }

        return $updated;
    }

    function getIngredientById($id)
    {
        return $this->IngredientManagementModelProxy->getIngredientById($id);
    }

    function deleteIngredient($id)
    {
        $ingredient = $this->IngredientManagementModelProxy->getIngredientById($id);
        if ($ingredient) {
            ImageHelper::removeImage($ingredient->featured_img);
        }
        return $this->IngredientManagementModelProxy->deleteIngredient($id);
    }

    function getAllWithoutPagination()
    {
        return $this->IngredientManagementModelProxy->getAllWithoutPagination();
    }

    function getIngredientBySlug($slug)
    {
        return $this->IngredientManagementModelProxy->getIngredientBySlug($slug);
    }
}
