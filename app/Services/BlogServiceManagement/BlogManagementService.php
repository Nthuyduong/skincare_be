<?php

namespace App\Services\BlogServiceManagement;

use App\Exceptions\SlugExistException;
use App\Helpers\ImageHelper;
use App\Jobs\SendMailNotication;
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
        return $this->blogManagementModelProxy->getBlogById($id, true);
    }

    function getBlogBySlug($slug)
    {
        return $this->blogManagementModelProxy->getBlogBySlug($slug);
    }

    function updateBlog($id, $data)
    {
        $blog = $this->blogManagementModelProxy->getBlogById($id);

        $locale = app()->getLocale();
        
        if ($blog) {
            if ($locale && $locale != 'en') {
                return $this->blogManagementModelProxy->createOrUpdateBlogTran($id, $data);
            } else {
                if (isset($data['featured_img'])) {
                    $featured_img = ImageHelper::resizeImage($data['featured_img']);
                    $data['featured_img'] = $featured_img['original'];
                }
                if (isset($data['banner_img'])) {
                    $banner_img = ImageHelper::resizeImage($data['banner_img']);
                    $data['banner_img'] = $banner_img['original'];
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
        }
        return null;
    }

    function publishBlog($id)
    {
        $blog = $this->blogManagementModelProxy->publishBlog($id);

        if (!empty($blog)) {
            if (empty($blog['old_publish_date'])) {
                $job = new SendMailNotication($blog['id']);
                dispatch($job);
            }
        }

        return $blog;
    }

    function updateStatusBlogs($ids, $status)
    {
        return $this->blogManagementModelProxy->updateStatusBlogs($ids, $status);
    }

    function updateViewCount($id)
    {
        return $this->blogManagementModelProxy->updateViewCount($id);
    }

    function updateShareCount($id)
    {
        return $this->blogManagementModelProxy->updateShareCount($id);
    }

    function deleteBlogs($ids)
    {
        return $this->blogManagementModelProxy->deleteBlogs($ids);
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

    function getRelatedBlogs($id) {
        return $this->blogManagementModelProxy->getRelatedBlogs($id);
    }

    function getBlogsByCategoryId($id, $page = 1, $limit = 10, $filter = []) {
        return $this->blogManagementModelProxy->getBlogsByCategoryId($id, $page, $limit, $filter);
    }

    function getByTags($tags, $page = 1, $limit = 10, $filter = []) {
        return $this->blogManagementModelProxy->getByTags($tags, $page, $limit, $filter);
    }

    function getBlogsByCategorySlug($slug, $page = 1, $limit = 10, $filter = []) {
        return $this->blogManagementModelProxy->getBlogsByCategorySlug($slug, $page, $limit, $filter);
    }

    function getComments($id, $page = 1, $limit = 10, $filter = []) {
        return $this->blogManagementModelProxy->getComments($id, $page, $limit, $filter);
    }

    function createComment($data) {
        return $this->blogManagementModelProxy->createComment($data);
    }
    function createCommentGuest($data) {
        return $this->blogManagementModelProxy->createCommentGuest($data);
    }

    function deleteComment($id) {
        return $this->blogManagementModelProxy->deleteComment($id);
    }

    function updateComment($id, $data) {
        return $this->blogManagementModelProxy->updateComment($id, $data);
    }

    function getLikes($id) {
        return $this->blogManagementModelProxy->getLikes($id);
    }

    function handleLike($id, $user_id) {
        $isLike = $this->blogManagementModelProxy->isLiked($id, $user_id);
        if ($isLike) {
            return $this->blogManagementModelProxy->unLike($id, $user_id);
        } else {
            return $this->blogManagementModelProxy->like($id, $user_id);
        }
    }

    function isLiked($id, $user_id) {
        return $this->blogManagementModelProxy->isLiked($id, $user_id);
    }
}
