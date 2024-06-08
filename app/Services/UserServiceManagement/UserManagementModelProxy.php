<?php

namespace App\Services\UserServiceManagement;

use App\Models\User;
use App\Models\Provider;
use Illuminate\Support\Facades\DB;

class UserManagementModelProxy {
    function createUser($data){
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->save();
        return $user;
    }

    function createUserWithSocial($data){
        return DB::transaction(function () use ($data){
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->avatar = $data['avatar'];
            $user->save();
            $provider = new Provider();
            $provider->user_id = $user->id;
            $provider->provider = $data['provider'];
            $provider->provider_id = $data['provider_id'];
            $provider->avatar = $data['avatar'];
            $provider->save();
            return $user;
        });
    }

    function getUserByEmail($email){
        return User::where('email', $email)->first();
    }

    function addUserProvider($data){
        $provider = new Provider();
        $provider->user_id = $data['user_id'];
        $provider->provider = $data['provider'];
        $provider->provider_id = $data['provider_id'];
        $provider->avatar = $data['avatar'];
        $provider->save();

    }
    function getUserProviders($user_id){
        return Provider::where('user_id', $user_id)->get();
    }

    function getUserProvider($provider, $provider_id){
        return Provider::where('provider', $provider)
            ->where('provider_id', $provider_id)
            ->first();
    }

    function getUser($id){
        return User::find($id);
    }

    function getAllUserWithFilter($page = 1, $limit = 10, $filter = []){
        $query = User::query();

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

    function deleteUser($id){
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }
}