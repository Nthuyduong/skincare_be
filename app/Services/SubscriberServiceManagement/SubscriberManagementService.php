<?php

namespace App\Services\SubscriberServiceManagement;
use App\Jobs\SendMailJob;
use App\Exceptions\ExceptionMessage;
class SubscriberManagementService
{
    protected $subscriberManagementProxy;

    public function __construct(SubscriberManagementModelProxy $subscriberManagementProxy)
    {
        $this->subscriberManagementProxy = $subscriberManagementProxy;
    }

    public function subscribe($data)
    {
        $check = $this->subscriberManagementProxy->getSubscribeByEmail($data['email']);
        if ($check) {
            throw new ExceptionMessage('Email already subscribed');
        }
        $sub = $this->subscriberManagementProxy->createSubscribe($data);
        $job = new SendMailJob($sub->email, 'Subscribe', 'Thank you for subscribing');
        dispatch($job);
        return $sub;
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