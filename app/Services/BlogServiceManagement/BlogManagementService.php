<?php

namespace App\Services\BlogServiceManagement;

use App\Exceptions\SlugExistException;
use App\Helpers\ImageHelper;
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
            $featured_img = ImageHelper::resizeImage($data['featured_img']);
            $data['featured_img'] = $featured_img['original'];
        }
        if (isset($data['banner_img'])) {
            $banner_img = ImageHelper::resizeImage($data['banner_img']);
            $data['banner_img'] = $banner_img['original'];
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
        $blog = $this->blogManagementModelProxy->getBlogById($id);
        
        if ($blog) {
            if (isset($data['featured_img'])) {
                $featured_img = ImageHelper::resizeImage($data['featured_img']);
                $data['featured_img'] = $featured_img['original'];
            }
            if (isset($data['banner_img'])) {
                $banner_img = ImageHelper::resizeImage($data['banner_img']);
                $data['banner_img'] = $banner_img['original'];
            }
        }
        $updateBLog = $this->blogManagementModelProxy->updateBlog($id, $data);
        if (isset($data['featured_img']) && $blog->featured_img) {
            ImageHelper::removeImage($blog->featured_img);
        }
        if (isset($data['banner_img']) && $blog->banner_img) {
            ImageHelper::removeImage($blog->banner_img);
        }
        return $updateBLog;
    }

    function deleteBlog($id)
    {
        return $this->blogManagementModelProxy->deleteBlog($id);
    }

    function getNewest($data) {
        return $this->blogManagementModelProxy->getNewest($data);
    }

    function getPopular($data) {
        return $this->blogManagementModelProxy->getPopular($data);
    }
}
