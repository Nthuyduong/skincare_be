<?php

namespace App\Services\SubcateServiceManagement;

use App\Models\Subcate;
use Illuminate\Support\Facades\Log;
use DateTime;

class SubcateManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {


        $count = $query->count();

        if (isset($filter['search'])) {
            $query = $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $filter['search'] . '%');
            });
        }
    }

    function createBlog($data)
    {

    }

    function checkSlugExist($slug)
    {
        
    }
}

