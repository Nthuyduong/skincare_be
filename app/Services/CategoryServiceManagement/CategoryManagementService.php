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
        $categories = $this->CategoryManagementModelProxy->getAllWithFilter($page, $limit, $filter);
        return $categories;
    }

    function createCategory($data)
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
        return $this->CategoryManagementModelProxy->createCategory($data);
    }

    function updateCategory($id, $data)
    {
        $categories = $this->CategoryManagementModelProxy->updateCategory($id, $data);

        return $categories;
    }
}
