<?php

namespace Api\view;

use Api\model\User;

class UserView
{
    static function renderOneUser(User $user)
    {
        $array['users'] = array();
        array_push($array['users'], [
            "id" => $user->getId(),
            "first_name" => $user->getFirstName(),
            "last_name" => $user->getLastName(),
            "money" => $user->getMoney(),
            'password'=>$user->getPassword()
        ]);
        return $array;
    }

    static function renderAllUsers(array $users)
    {
        $array['users'] = array();
        $num = count($users);
        for ($i = 0; $i < $num; $i++) {
            array_push($array['users'], [
                "id" => $users[$i]->getId(),
                "first_name" => $users[$i]->getFirstName(),
                "last_name" => $users[$i]->getLastName(),
                "money" => $users[$i]->getMoney(),
                'password'=>$users[$i]->getPassword()
            ]);
        }
        return $array;
    }
}