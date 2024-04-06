<?php

namespace App\Services\ContactServiceManagement;

use App\Models\Contact;

class ContactManagementProxy
{
    function createContact($data)
    {
        $contact = new Contact();
        $contact->name = $data['name'];
        $contact->email = $data['email'];
        $contact->message = $data['message'];
        $contact->save();
        return $contact;
    }

    function getContact($id)
    {
        return Contact::find($id);
    }

    function getAllContactWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Contact::query();

        $count = $query->count();

        if (isset($filter['search'])) {
            $query = $query->where('name', 'like', '%' . $filter['search'] . '%');
        }

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

    function deleteContact($id)
    {
        $contact = Contact::find($id);
        if ($contact) {
            $contact->delete();
            return true;
        }
        return false;
    }
}