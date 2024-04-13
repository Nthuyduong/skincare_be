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
            ->leftJoin('blog_details as detail', 'blogs.id', '=', 'detail.blog_id')
            ->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('detail.content', 'like', '%' . $search . '%')
                    ->orWhere('suggest', 'like', '%' . $search . '%');
            })
            ->select(
                'blogs.id as id',
                'title',
                'featured_img',
                'summary',
                'publish_date',
                'slug',
                'suggest',
                DB::raw("'blogs' as table_name"),
            );

        $queryIngredient = Ingredient::query()
            ->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('suggest', 'like', '%' . $search . '%');
            })
            ->select(
                'id',
                'name as title',
                'featured_img',
                'description as summary',
                'publish_date',
                'suggest',
                DB::raw("'' as slug"),
                DB::raw("'ingredient' as table_name"),
            );

        $combine = $queryBlog
            ->unionAll($queryIngredient);
        $count = $combine->count();
        $results = $combine->skip(($page - 1) * $limit)
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