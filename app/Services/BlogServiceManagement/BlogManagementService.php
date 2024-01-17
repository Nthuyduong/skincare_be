<?php

namespace App\Services\BlogServiceManagement;

use App\Exceptions\SlugExistException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class BlogManagementService
{

    protected $blogManagementModelProxy;

    public function __construct(BlogManagementModelProxy $blogManagementModelProxy)
    {
        $this->blogManagementModelProxy = $blogManagementModelProxy;
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $blogs = $this->blogManagementModelProxy->getAllWithFilter($page, $limit, $filter);
        return $blogs;
    }

    function createBlog($data)
    {
        $checkSlug = $this->blogManagementModelProxy->checkSlugExist($data['slug']);
        if ($checkSlug) {
            throw new SlugExistException();
        }

        if (isset($data['featured_img'])) {
            $fileFolder = '/storage';
            if (!File::exists($fileFolder)) {
                File::makeDirectory(public_path($fileFolder), 0777, true, true);
            }

            $file = $data['featured_img'];
            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path($fileFolder), $fileName);

            $data['featured_img'] = $fileFolder . '/' . $fileName;
        }
        return $this->blogManagementModelProxy->createBlog($data);
    }

    function getBlogById($id)
    {
        return $this->blogManagementModelProxy->getBlogById($id);
    }

    function getBlogBySlug($slug)
    {
        return $this->blogManagementModelProxy->getBlogBySlug($slug);
    }

    function updateBlog($id, $data)
    {
        return $this->blogManagementModelProxy->updateBlog($id, $data);
    }

    function deleteBlog($id)
    {
        return $this->blogManagementModelProxy->deleteBlog($id);
    }
}
