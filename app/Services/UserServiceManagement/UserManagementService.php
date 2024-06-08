<?php

namespace App\Services\UserServiceManagement;

class UserManagementService
{
    private $userManagementModelProxy;

    public function __construct(UserManagementModelProxy $userManagementModelProxy)
    {
        $this->userManagementModelProxy = $userManagementModelProxy;
    }

    function createUser($data)
    {
        $user = $this->userManagementModelProxy->getUserByEmail($data['email']);
        $provider = $this->userManagementModelProxy->getUserProvider($data['provider'], $data['provider_id']);
        $newUser = null;
        if (empty($user) && empty($provider)) {
            $newUser = $this->userManagementModelProxy->createUserWithSocial($data);
        } else {
            $newUser = $user;
            if (empty($provider)) {
                $data['user_id'] = $newUser->id;
                $this->userManagementModelProxy->addUserProvider($data);
            }
            if (empty($user->avatar)) {
                $user->avatar = $data['avatar'];
                $user->save();
            }
            $newUser->save();
        }
        return $newUser;
    }

    function createOrUpdateUserWithSocial($data)
    {
        return $this->userManagementModelProxy->createUserWithSocial($data);
    }

    function getUserByEmail($email)
    {
        return $this->userManagementModelProxy->getUserByEmail($email);
    }

    function getUser($id)
    {
        return $this->userManagementModelProxy->getUser($id);
    }

    function getAllUserWithFilter($page = 1, $limit = 10, $filter = [])
    {
        return $this->userManagementModelProxy->getAllUserWithFilter($page, $limit, $filter);
    }
}