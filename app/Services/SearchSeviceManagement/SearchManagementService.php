<?php

namespace App\Services\SearchSeviceManagement;

class SearchManagementService
{
    protected $SearchManagementModelProxy;

    public function __construct(SearchManagementModelProxy $searchManagementModelProxy) {
        $this->SearchManagementModelProxy = $searchManagementModelProxy;
    }

    function search($page = 1, $limit = 10, $search) {
        return $this->SearchManagementModelProxy->search($page, $limit, $search);
    }

}