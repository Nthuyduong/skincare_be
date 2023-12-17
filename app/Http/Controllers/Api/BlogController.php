<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SlugExistException;
use App\Http\Controllers\ApiController;;
use App\Models\Blog;
use App\Services\BlogServiceManagement\BlogManagementService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BlogController extends ApiController
{

    protected $blogManagementService;

    public function __construct(BlogManagementService $blogManagementService)
    {
        $this->blogManagementService = $blogManagementService;
    }


    // ex: http://127.0.0.1:8000/api/blogs?page=1&limit=10&search=0&status=1&publish_to=2023-01-18&publish_from=2023-01-16&sort=publish_date:desc
    public function getAll(Request $request)
    {
        try {

            $page = $request->input('page', 1);
            if ($page < 1){
                $page = 1;
            }
            $limit = $request->input('limit', 10);
            if ($limit < 1){
                $limit = 10;
            }
            $filter = [];
            $filter['search'] = $request->input('search');
            $filter['status'] = $request->input('status');
            $filter['sort'] = $request->input('sort');
            $filter['publish_from'] = $request->input('publish_from');
            $filter['publish_to'] = $request->input('publish_to');

            $blogs = $this->blogManagementService->getAllWithFilter($page, $limit, $filter);

            return response()->json([
                'data' => $blogs,
                'status' => self::STATUS_SUCCESS,
                'msg' => 'success',
            ]);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    // note nhớ sau khi pull code về chạy lệnh php artisan migrate:fresh để reset lại database 
    // vì lười lỡ sửa vào migrate đã chạy rồi
    // đúng ra nên tạo 1 file migration mới để sửa
    public function createBlog(Request $request)
    {
        try {

            $this->validate($request, [
                'title' => 'required',
                'content' => 'required',
                'slug' => 'required',
                'status' => 'required',
            ]);
            $data = [];
            $data['title'] = $request->input('title');
            $data['content'] = $request->input('content');
            $data['content_draft'] = $request->input('content_draft');
            $data['summary'] = $request->input('summary');
            $data['tag'] = $request->input('tag');
            $data['slug'] = $request->input('slug');
            $data['status'] = $request->input('status');
            $data['author'] = $request->input('author');
            $data['publish_date'] = $request->input('publish_date');
            $data['featured_img'] = $request->file('featured_img');

            $blog = $this->blogManagementService->createBlog($data);

            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => 'test created',
            ]);
        } catch(ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch(SlugExistException $e) {
            return $this->clientErrorResponse($e->getMessage(), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }
    // GET http://127.0.0.1:8000/api/blogs/1
    public function getBlogById(string $id)
    {
        try {

            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => $id,
            ]);
        } catch(ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    // PUT http://127.0.0.1:8000/api/blogs/1
    public function updateBlog(Request $request, string $id)
    {
        try {



            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'msg' => $id,
            ]);
        } catch(ValidationException $e) {
            return $this->clientErrorResponse('Invalid request: ' . json_encode($e->errors()), \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->internalServerErrorResponse(__METHOD__, $e);
        }
    }

    public function deleteBlog(Request $request)
    {

    }

}
