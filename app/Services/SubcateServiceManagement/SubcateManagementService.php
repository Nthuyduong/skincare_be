<?php

namespace App\Services\SubcateServiceManagement;

use App\Exceptions\SlugExistException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SubcateManagementService
{

    protected $subcateManagementModelProxy;

    public function __construct(subcateManagementModelProxy $subcateManagementModelProxy)
    {
        $this->subcateManagementModelProxy = $subcateManagementModelProxy;
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {

    }

    function createSubcate($data)
    {

    }

}
