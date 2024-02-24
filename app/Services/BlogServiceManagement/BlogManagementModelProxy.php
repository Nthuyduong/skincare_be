<?php

namespace App\Services\BlogServiceManagement;

use App\Models\Blog;
use Illuminate\Support\Facades\Log;
use DateTime;

class BlogManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Blog::query();

        // select * from blogs
        $count = $query->count();

        $query = $query->with('categories');

        // check nếu request có filter có search thì thêm vào query
        if (isset($filter['search'])) {
            // gom nhóm các điều kiện trong where lại
            $query = $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $filter['search'] . '%');
            });
        }

        if (isset($filter['status'])) {
            $query = $query->where('status', $filter['status']);
        }

        if (isset($filter['publish_from'])) {
            $fromDate = new DateTime($filter['publish_from']);
            $query = $query->where('publish_date', '>=', $fromDate);
        }

        if (isset($filter['publish_to'])) {
            $toDate = new DateTime($filter['publish_to']);
            $query = $query->where('publish_date', '<=', $toDate);
        }

        if (isset($filter['category_id'])) {
            $query = $query->whereHas('categories', function ($q) use ($filter) {
                $q->where('category_id', $filter['category_id']);
            });
        }

        if (isset($filter['sort'])) {
            //ex: sort = id:desc or sort = id:asc,...
            $sort = $filter['sort'];
            // tách chuỗi sort theo dấu : trả về mảng gồm 2 phần tử trước và sau dấu :
            // $sortArr = ['id', 'desc']
            $sortArr = explode(':', $sort);
            // kiểm tra nếu mảng có 2 phần tử thì mới sắp xếp
            if (count($sortArr) == 2) {
                $query = $query->orderBy($sortArr[0], $sortArr[1]);
            }
        }

        // select * from blogs limit ($limit) offset (($page - 1) * $limit)
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
        // select * from blogs
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

    function createBlog($data)
    {
        $blog = new Blog();
        $blog->title = $data['title'];
        $blog->content = $data['content'];
        $blog->content_draft = $data['content_draft'];
        $blog->summary = $data['summary'];
        $blog->tag = $data['tag'];
        $blog->slug = $data['slug'];
        $blog->status = $data['status'];
        $blog->author = $data['author'];
        $blog->publish_date = $data['publish_date'];
        $blog->featured_img = $data['featured_img'];
        $blog->banner_img = $data['banner_img'];
        $blog->meta_title = $data['meta_title'];
        $blog->meta_description = $data['meta_description'];
        $blog->save();
        $blog->categories()->attach($data['categories']);
        return $blog;
    }

    function checkSlugExist($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if ($blog) {
            return true;
        }
        return false;
    }

    function getBlogById($id)
    {
        return Blog::where('id', $id)->with('categories')->first();
    }

    function getBlogBySlug($slug)
    {
        return Blog::where('slug', $slug)->with('categories')->first();
    }

    function updateBlog($id, $data)
    {
        $blog = $this->getBlogById($id);
        if (!$blog) {
            return null;
        }
        $blog->title = $data['title'] ?? $blog->title;
        $blog->content = $data['content'] ?? $blog->content;
        $blog->slug = $data['slug'] ?? $blog->slug;
        $blog->summary = $data['summary'] ?? $blog->summary;
        $blog->featured_img = $data['featured_img'] ?? $blog->featured_img;
        $blog->banner_img = $data['banner_img'] ?? $blog->banner_img;
        $blog->meta_title = $data['meta_title'] ?? $blog->meta_title;
        $blog->meta_description = $data['meta_description'] ?? $blog->meta_description;

        if (isset($data['categories'])) {
            $blog->categories()->sync($data['categories']);
        }
        $blog->save();
        return $this->getBlogById($id);
    }

    function deleteBlog($id)
    {
        $blog = $this->getBlogById($id);

        if (!$blog) {
            return null;
        }

        $blog->categories()->detach();

        $blog->delete();

        return true;
    }

    function getNewest($data) {
        return Blog::with('categories')->orderBy('publish_date', 'desc')->limit($data['limit'])->get();
    }

    function getPopular($data) {
        $query = Blog::with('categories');
        $query = $query->orderBy('view_count', 'desc');
        if (isset($data['days'])) {
            $fromDate = new DateTime();
            $fromDate->modify('-' . $data['days'] . ' day');
            $query = $query->where('publish_date', '>=', $fromDate);
        }
        return $query->limit($data['limit'])->get();
    }
}
