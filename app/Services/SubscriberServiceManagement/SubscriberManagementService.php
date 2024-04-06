<?php

namespace App\Services\SubscriberServiceManagement;

class SubscriberManagementService
{
    protected $subscriberManagementProxy;

    public function __construct(SubscriberManagementModelProxy $subscriberManagementProxy)
    {
        $this->subscriberManagementProxy = $subscriberManagementProxy;
    }

    public function subscribe($data)
    {
        return $this->subscriberManagementProxy->createSubscribe($data);
    }

    public function getSubscribe($id)
    {
        return $this->subscriberManagementProxy->getSubscribe($id);
    }

    public function getAllSubscribeWithFilter($page = 1, $limit = 10, $filter = [])
    {
        return $this->subscriberManagementProxy->getAllSubscribeWithFilter($page, $limit, $filter);
    }

    public function deleteSubscribe($id)
    {
        return $this->subscriberManagementProxy->deleteSubscribe($id);
    }
}