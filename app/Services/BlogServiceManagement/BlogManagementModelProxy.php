<?php

namespace App\Services\BlogServiceManagement;

use App\Models\Blog;
use App\Models\BlogDetail;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Facades\Log;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Models\BlogTran;
use App\Helpers\LocaleHelper;

class BlogManagementModelProxy
{
    public function __construct()
    {
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Blog::query();

        $locale = app()->getLocale();

        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('categories.' . $locale);
        } else {
            $query = $query->with('categories');
        }

        // check nếu request có filter có search thì thêm vào query
        if (isset($filter['search'])) {
            // gom nhóm các điều kiện trong where lại
            $query = $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('slug', 'like', '%' . $filter['search'] . '%')
                    ->orWhereHas('locales', function ($q) use ($slug) {
                        $q->where('slug', $slug);
                    });
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
            ->get()
            ->toArray();
        // select * from blogs
        return [
            'results' => LocaleHelper::convertBlogsToLocale($results, $locale ?? 'en'),
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

    function getBlogById($id, $isArray = false)
    {
        $locale = app()->getLocale();
        $query = Blog::where('id', $id)
            ->with('detail');
        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('categories.' . $locale);
        } else {
            $query = $query->with('categories');
        }
        if (!$isArray) {
            return $query->first();
        }

        return LocaleHelper::convertBlogToLocale($query->first()->toArray(), $locale);
            
    }

    function getBlogBySlug($slug)
    {
        $locale = app()->getLocale();
        $query = Blog::where('slug', $slug)
            ->orWhereHas('locales', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->with('detail');
            
        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('categories.' . $locale)
                ->with('categories.parent.' . $locale);
        } else {
            $query = $query->with('categories.parent');
        }
        $result = $query->first();
        if (!$result) {
            return null;
        }
        return LocaleHelper::convertBlogToLocale($result->toArray(), $locale);
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
            $blog->tag = $data['tag'];
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

    function createOrUpdateBlogTran($id, $data) {
        $locale = app()->getLocale();
        $blogTran = BlogTran::where('blog_id', $id)
            ->where('locale', $locale)
            ->first();
        if (!$blogTran) {
            $blogTran = new BlogTran();
            $blogTran->blog_id = $id;
            $blogTran->locale = $locale;
        }
        $blogTran->title = $data['title'] ?? $blogTran->title;
        $blogTran->summary = $data['summary'] ?? $blogTran->summary;
        $blogTran->tag = $data['tag'] ?? $blogTran->tag;
        $blogTran->slug = $data['slug'] ?? $blogTran->slug;
        $blogTran->meta_title = $data['meta_title'] ?? $blogTran->meta_title;
        $blogTran->meta_description = $data['meta_description'] ?? $blogTran->meta_description;
        $blogTran->excerpt = $data['excerpt'] ?? $blogTran->excerpt;
        $blogTran->suggest = $data['suggest'] ?? $blogTran->suggest;
        $blogTran->content_draft = $data['content'] ?? $blogTran->content_draft;
        if (!$blogTran->content) {
            $blogTran->content = $blogTran->content_draft;
        }
        $blogTran->save();

        return $blogTran;
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
            if (empty($blog->publish_date)) {
                $blog->publish_date = new DateTime();
            }

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

            $blogTrans = BlogTran::where('blog_id', $id)
                ->get();
            foreach ($blogTrans as $tran) {
                $tran->content = $tran->content_draft;
                $newTransContent = $tran->content;
                $domTran = new \DOMDocument();
                libxml_use_internal_errors(true);
                $domTran->loadHTML($newTransContent);
                libxml_clear_errors();
                $countCharTran = str_word_count($domTran->textContent);
                $tran->estimate_time = ceil($countCharTran / 238);
                $tran->save();
            }

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

        $locale = app()->getLocale();
        $query = Blog::query();
        
        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('categories.' . $locale);
        } else {
            $query = $query->with('categories');
        }

        $query = $query->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag', 'estimate_time'
            )
            ->orderBy('publish_date', 'desc')
            ->limit($data['limit'])
            ->get();

        return LocaleHelper::convertBlogsToLocale($query->toArray(), $locale ?? 'en');

    }

    function getPopular($data) {
        $query = Blog::with('categories');
        $locale = app()->getLocale();
        if ($locale && $locale != 'en') {
            $query = $query->with($locale);
        }
        $query = $query->orderBy('view_count', 'desc');
        if (isset($data['days'])) {
            $fromDate = new DateTime();
            $fromDate->modify('-' . $data['days'] . ' day');
            $query = $query->where('publish_date', '>=', $fromDate);
        }
        $query = $query
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag', 'estimate_time'
            )
            ->limit($data['limit'])->get();

        return LocaleHelper::convertBlogsToLocale($query->toArray(), $locale ?? 'en');
    }

    function getRelatedBlogs($id) {
        $locale = app()->getLocale();
        $blog = $this->getBlogById($id, true);
        if (!$blog) {
            return [];
        }
        $categories = array_column($blog['categories'], 'id');
        $blogs = Blog::query();
        if ($locale && $locale != 'en') {
            $blogs = $blogs->with($locale)
                ->with('categories.' . $locale);
        } else {
            $blogs = $blogs->with('categories');
        }

        $blogs = $blogs->where(function($q) use ($blog, $categories) {
                $q->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('category_id', $categories);
                });
                $tags = explode(',', $blog['tag']);
                foreach ($tags as $tag) {
                    $q->orWhere('tag', 'like', '%' . $tag . '%');
                }
            })
            ->where('id', '!=', $id)
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag', 'estimate_time'
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
        return LocaleHelper::convertBlogsToLocale($blogs->toArray(), $locale);
    }

    function getBlogsByCategoryId($id, $page = 1, $limit = 10, $filter = []) {

        $locale = app()->getLocale();
        $query = Blog::with('categories');

        if ($locale && $locale != 'en') {
            $query = $query->with($locale);
        }
        $query = $query->whereHas('categories', function ($q) use ($id) {
                $q->where('category_id', $id);
            })
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag', 'estimate_time'
            );
        $count = $query->count();
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
            $sortArr = explode(':', $sort);
            if (count($sortArr) == 2) {
                $query = $query->orderBy($sortArr[0], $sortArr[1]);
            }
        }
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
        return [
            'results' => LocaleHelper::convertBlogsToLocale($results->toArray(), $locale ?? 'en'),
            'paginate' => [
                'current' => $page,
                'limit' => $limit,
                'last' => ceil($count / $limit),
                'count' => $count,
            ]
        ];
    }

    function getByTags($tag, $page = 1, $limit = 10, $filter = []) {
        $locale = app()->getLocale();
        $query = Blog::with('categories');

        if ($locale && $locale != 'en') {
            $query = $query->with($locale);
        }

        $query = $query->where('tag', 'like', '%' . $tag . '%')
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag', 'estimate_time'
            );
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
            $sortArr = explode(':', $sort);
            if (count($sortArr) == 2) {
                $query = $query->orderBy($sortArr[0], $sortArr[1]);
            }
        }
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
        return LocaleHelper::convertBlogsToLocale($results->toArray(), $locale ?? 'en');
    }

    function getBlogsByCategorySlug($slug, $page = 1, $limit = 10, $filter = []) {
        $locale = app()->getLocale();
        $query = Blog::query();

        if ($locale && $locale != 'en') {
            $query = $query->with($locale)
                ->with('categories.' . $locale);
        } else {
            $query->with('categories');
        }

        $query = $query->whereHas('categories', function ($q) use ($slug) {
                $q->where('slug', $slug)
                    ->orWhereHas('locales', function ($q) use ($slug) {
                        $q->where('slug', $slug);
                    });
            })
            ->select(
                'id', 'title', 'slug', 'status', 'publish_date', 'view_count', 'created_at', 'updated_at',
                'meta_title', 'meta_description', 'featured_img', 'banner_img', 'author', 'summary', 'tag', 'estimate_time'
            );
        $count = $query->count();
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
            $sortArr = explode(':', $sort);
            if (count($sortArr) == 2) {
                $query = $query->orderBy($sortArr[0], $sortArr[1]);
            }
        }
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
        return [
            'results' => LocaleHelper::convertBlogsToLocale($results->toArray(), $locale ?? 'en'),
            'paginate' => [
                'current' => $page,
                'limit' => $limit,
                'last' => ceil($count / $limit),
                'count' => $count,
            ]
        ];
    }

    function getComments($id, $page = 1, $limit = 10, $filter = []) {
        $query = Comment::where('blog_id', $id)
            ->with('user')
            ->where('status', Comment::STATUS_SHOW);
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
            $sortArr = explode(':', $sort);
            if (count($sortArr) == 2) {
                $query = $query->orderBy($sortArr[0], $sortArr[1]);
            }
        }

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

    function createComment($data) {
        $comment = new Comment();
        $comment->user_id = $data['user_id'];
        $comment->blog_id = $data['blog_id'];
        $comment->content = $data['content'];
        $comment->status = Comment::STATUS_SHOW;
        $comment->save();
        return Comment::where('id', $comment->id)
            ->with('user')
            ->first();
    }

    function createCommentGuest($data) {
        $comment = new Comment();
        $comment->blog_id = $data['blog_id'];
        $comment->content = $data['content'];
        $comment->status = Comment::STATUS_SHOW;
        $comment->name = $data['name'];
        $comment->email = $data['email'];
        $comment->type = Comment::TYPE_GUEST;
        $comment->save();
        return $comment;
    }

    function deleteComment($id) {
        $comment = Comment::find($id);
        if ($comment) {
            $comment->delete();
            return true;
        }
        return false;
    }

    function updateComment($id, $data) {
        $comment = Comment::find($id);
        if ($comment) {
            $comment->content = $data['content'];
            $comment->save();
            return $comment;
        }
        return null;
    }

    function isLiked($blogId, $userId) {
        $like = Like::where('blog_id', $blogId)
            ->where('user_id', $userId)
            ->first();
        if ($like) {
            return true;
        }
        return false;
    }

    function getLikes($id) {
        $likes = Like::where('blog_id', $id)
            ->with('user')
            ->get();
        return $likes;
    }

    function like($data) {
        $like = new Like();
        $like->user_id = $data['user_id'];
        $like->blog_id = $data['blog_id'];
        $like->save();
        return $like;
    }

    function unLike($id) {
        $like = Like::find($id);
        if ($like) {
            $like->delete();
            return true;
        }
        return false;
    }
}
