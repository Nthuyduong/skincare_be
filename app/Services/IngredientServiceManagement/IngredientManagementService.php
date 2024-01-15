<?php

namespace App\Services\IngredientServiceManagement;

use App\Exceptions\SlugExistException;
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
        $ingredients = $this->IngredientManagementModelProxy->getAllWithFilter($page, $limit, $filter);
        return $ingredients;
    }

    function createIngredient($data)
    {
        if (isset($data['feature_img'])) {
            $fileFolder = '/uploads';
            if (!File::exists($fileFolder)) {
                File::makeDirectory(public_path($fileFolder), 0777, true, true);
            }

            $file = $data['feature_img'];
            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path($fileFolder), $fileName);

            $data['feature_img'] = $fileFolder . '/' . $fileName;
        }
        return $this->IngredientManagementModelProxy->createIngredient($data);
    }

    function updateCategory($id, $data)
    {
        $ingredients = $this->IngredientManagementModelProxy->updateCategory($id, $data);

        return $ingredients;
    }
}
