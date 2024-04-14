<?php

namespace App\Services\BlogServiceManagement;

use App\Models\Blog;
use App\Models\BlogDetail;
use Illuminate\Support\Facades\Log;
use DateTime;
use Illuminate\Support\Facades\DB;

class BlogManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Blog::query();

        

        $query = $query
            ->with('categories');

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
        // select * from blogs
        $count = $query->count();
        // select * from blogs limit ($limit) offset (($page - 1) * $limit)
        $results = $query
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag', 'estimate_time'
            )
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
        return DB::transaction(function () use ($data){
            // tạo mới blog
            $blog = new Blog();
            $blog->title = $data['title'];
            $blog->summary = $data['summary'];
            $blog->tag = $data['tag'];
            $blog->slug = $data['slug'];
            $blog->status = $data['status'];
            $blog->author = $data['author'];
            if ($data['status'] == Blog::STATUS_SHOW) {
                $blog->publish_date = new DateTime();
            }
            $blog->featured_img = $data['featured_img'];
            $blog->banner_img = $data['banner_img'];
            $blog->meta_title = $data['meta_title'];
            $blog->meta_description = $data['meta_description'];
            $blog->excerpt = $data['excerpt'];
            $blog->estimate_time = $data['estimate_time'];
            $blog->suggest = $data['suggest'];
            $blog->save();

            // tạo mới blog detail
            $detail = new BlogDetail();
            $detail->content = $data['content'];
            $detail->content_draft = $data['content'];
            $blog->detail()->save($detail);

            // thêm category cho blog
            $blog->categories()->attach($data['categories']);

            return $blog;
        });
        
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
        return Blog::where('id', $id)
            ->with('categories')
            ->with('detail')
            ->first();
    }

    function getBlogBySlug($slug)
    {
        return Blog::where('slug', $slug)
            ->with('detail')
            ->with('categories.parent')
            ->first();
    }

    function updateBlog($id, $data)
    {
        return DB::transaction(function() use ($id, $data){
            $blog = $this->getBlogById($id);
            if (!$blog) {
                return null;
            }
            $blog->title = $data['title'] ?? $blog->title;
            $blog->slug = $data['slug'] ?? $blog->slug;
            $blog->summary = $data['summary'] ?? $blog->summary;
            $blog->featured_img = $data['featured_img'] ?? $blog->featured_img;
            $blog->banner_img = $data['banner_img'] ?? $blog->banner_img;
            $blog->meta_title = $data['meta_title'] ?? $blog->meta_title;
            $blog->meta_description = $data['meta_description'] ?? $blog->meta_description;
            $blog->author = $data['author'] ?? $blog->author;
            $blog->tag = $data['tag'] ?? $blog->tag;
            $blog->status = $data['status'] ?? $blog->status;
            $blog->excerpt = $data['excerpt'] ?? $blog->excerpt;
            $blog->estimate_time = $data['estimate_time'] ?? $blog->estimate_time;
            $blog->suggest = $data['suggest'] ?? $blog->suggest;
    
            if (isset($data['content'])) {
                $blog->detail->content_draft = $data['content'];
                $blog->detail->save();
            }
    
            if (isset($data['categories'])) {
                $blog->categories()->sync($data['categories']);
            }
            $blog->save();
            return $this->getBlogById($id);
        });
    }

    function publishBlog($id)
    {
        return DB::transaction(function() use ($id){
            $blog = $this->getBlogById($id);
            $oldPublishDate = $blog->publish_date;
            if (!$blog) {
                return null;
            }
            $blog->status = Blog::STATUS_SHOW;
            $blog->publish_date = new DateTime();

            $blog->detail->content = $blog->detail->content_draft;
            $newContent = $blog->detail->content;
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($newContent);
            libxml_clear_errors();
            $countChar = str_word_count($dom->textContent);
            $blog->estimate_time = ceil($countChar / 238);
            $blog->detail->save();

            $blog->save();

            return [
                'id' => $blog->id,
                'status' => $blog->status,
                'publish_date' => $blog->publish_date,
                'old_publish_date' => $oldPublishDate ?? '',
            ];
        });
        
    }

    function updateStatusBlogs($ids, $status)
    {
        return Blog::whereIn('id', $ids)->update(['status' => $status]);
    }

    function updateViewCount($id)
    {
        return Blog::where('id', $id)->increment('view_count');
    }

    function updateShareCount($id)
    {
        return Blog::where('id', $id)->increment('share_count');
    }

    function deleteBlogs($ids)
    {
        foreach ($ids as $id) {
            $blog = $this->getBlogById($id);
            if ($blog) {
                $blog->categories()->detach();
                $blog->detail()->delete();
            }
        }
        return Blog::whereIn('id', $ids)->delete();
    }

    function deleteBlog($id)
    {
        $blog = $this->getBlogById($id);

        if (!$blog) {
            return null;
        }

        $blog->categories()->detach();
        $blog->detail()->delete();
        $blog->delete();

        return true;
    }

    function getNewest($data) {
        return Blog::with('categories')
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag',
            )
            ->orderBy('publish_date', 'desc')
            ->limit($data['limit'])->get();
    }

    function getPopular($data) {
        $query = Blog::with('categories');
        $query = $query->orderBy('view_count', 'desc');
        if (isset($data['days'])) {
            $fromDate = new DateTime();
            $fromDate->modify('-' . $data['days'] . ' day');
            $query = $query->where('publish_date', '>=', $fromDate);
        }
        return $query
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag',
            )
            ->limit($data['limit'])->get();
    }

    function getRelatedBlogs($id) {
        $blog = $this->getBlogById($id);
        if (!$blog) {
            return [];
        }
        $categories = $blog->categories->pluck('id');
        $blogs = Blog::with('categories')
            ->where(function($q) use ($blog, $categories) {
                $q->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('category_id', $categories);
                });
                $tags = explode(',', $blog->tag);
                foreach ($tags as $tag) {
                    $q->orWhere('tag', 'like', '%' . $tag . '%');
                }
            })
            ->where('id', '!=', $id)
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag',
            )
            ->limit(6)->get();
        if ($blogs->count() < 6) {
            $ids = $blogs->pluck('id');
            $newBlogs = Blog::with('categories')
                ->where('id', '!=', $id)
                ->whereNotIn('id', $ids)
                ->select(
                    'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                    'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag',
                )
                ->limit(6 - $blogs->count())->get();
            $blogs = $blogs->merge($newBlogs);
        }
        return $blogs;
    }

    function getBlogsByCategoryId($id, $page = 1, $limit = 10) {

        $query = Blog::with('categories')
            ->whereHas('categories', function ($q) use ($id) {
                $q->where('category_id', $id);
            })
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag',
            );
        $count = $query->count();
        $results = $query
            ->skip(($page - 1) * $limit)
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
