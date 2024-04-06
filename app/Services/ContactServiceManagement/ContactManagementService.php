<?php

namespace App\Services\ContactServiceManagement;

use App\Jobs\SendMailJob;

class ContactManagementService
{
    protected $contactManagementProxy;

    public function __construct(ContactManagementProxy $contactManagementProxy)
    {
        $this->contactManagementProxy = $contactManagementProxy;
    }

    public function createContact($data)
    {
        $contact = $this->contactManagementProxy->createContact($data);

        $job = new SendMailJob($contact->email, 'Contact', 'Thank you for contacting us');
        dispatch($job);
        return $contact;
    }

    public function getContact($id)
    {
        return $this->contactManagementProxy->getContact($id);
    }

    public function getAllContactWithFilter($page = 1, $limit = 10, $filter = [])
    {
        return $this->contactManagementProxy->getAllContactWithFilter($page, $limit, $filter);
    }

    public function deleteContact($id)
    {
        return $this->contactManagementProxy->deleteContact($id);
    }
}