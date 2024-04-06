<?php

namespace App\Services\SubscriberServiceManagement;

use App\Models\Subscribes;

class SubscriberManagementModelProxy
{
    function createSubscribe($data)
    {
        $subscribe = new Subscribes();
        $subscribe->email = $data['email'];
        $subscribe->save();
        return $subscribe;
    }

    function getSubscribe($id)
    {
        return Subscribes::find($id);
    }

    function getAllSubscribeWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $query = Subscribes::query();

        $count = $query->count();

        if (isset($filter['search'])) {
            $query = $query->where('email', 'like', '%' . $filter['search'] . '%');
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

    function deleteSubscribe($id)
    {
        $subscribe = Subscribes::find($id);
        if ($subscribe) {
            $subscribe->delete();
            return true;
        }
        return false;
    }

    function getAll() {
        return Subscribes::all();
    }
}

