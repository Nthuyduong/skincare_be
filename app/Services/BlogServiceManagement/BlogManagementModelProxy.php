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
        $blog->save();
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
        $blog = Blog::find($id);

        return $blog;
    }

    function updateBlog($id, $data)
    {
        $blog = $this->getBlogById($id);

        if (!$blog) {
            return null;
        }

        $blog->update($data);
        return $blog;
    }

    function deleteBlog($id)
    {
        $blog = $this->getBlogById($id);

        if (!$blog) {
            return null;
        }

        $blog->delete();
        return true;
    }
}
