<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\BlogServiceManagement\BlogManagementService;
use Illuminate\Http\Request;

class BlogController extends Controller
{

    protected $blogManagementService;

    public function __construct(BlogManagementService $blogManagementService)
    {
        $this->blogManagementService = $blogManagementService;
    }


    public function getAll(Request $request)
    {

    }


    public function createBlog(Request $request)
    {

    }

    public function updateBlog(Request $request)
    {

    }

    public function deleteBlog(Request $request)
    {

    }

}
