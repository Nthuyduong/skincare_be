<?php

namespace App\Services\SearchSeviceManagement;

use App\Models\Blog;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchManagementModelProxy
{
    function search($page = 1, $limit = 10, $search)
    {
        
        $queryBlog = Blog::query()
            ->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%');
            })
            ->select(
                'id',
                'title',
                'featured_img',
                'summary',
                'content',
                'publish_date',
                DB::raw("'blogs' as table_name"),
            );

        $queryIngredient = Ingredient::query()
            ->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhereHas('details', function($qChild) use($search) {
                        $qChild->where('name', 'like', '%' . $search . '%')
                            ->orWhere('content', 'like', '%' . $search . '%');
                    });
            })
            ->select(
                'id',
                'name as title',
                'featured_img',
                'description as summary',
                'content',
                'publish_date',
                DB::raw("'ingredient' as table_name"),
            );

        $results = $queryBlog
            ->unionAll($queryIngredient)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return $results;
    }
}