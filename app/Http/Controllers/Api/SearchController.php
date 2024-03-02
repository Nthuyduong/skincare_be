<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\ApiController;
use App\Services\SearchSeviceManagement\SearchManagementService;
use Illuminate\Http\Request;

class SearchController extends ApiController
{

    protected $searchManagementService;

    public function __construct(SearchManagementService $searchManagementService)
    {
        $this->searchManagementService = $searchManagementService;
    }

    public function search(Request $request)
    {
        $page = $request->input('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $limit = $request->input('limit', 10);
        if ($limit < 1) {
            $limit = 10;
        }

        $search = $request->input('search');

        $data = $this->searchManagementService->search($page, $limit, $search);

        return response()->json([
            'data' => $data,
            'status' => self::STATUS_SUCCESS,
            'msg' => 'search'
        ]);
    }
}