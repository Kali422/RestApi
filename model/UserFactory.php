<?php

namespace Api\model;

class UserFactory
{
    static function createUsersFromRequest($fileIn)
    {
        $output = array();
        if ($input = isFile($fileIn)) {
            //$input = json_decode(file_get_contents($fileIn));
            $num = count($input->users);
            for ($i = 0; $i < $num; $i++) {
                if (isset($input->users[$i]->first_name) && isset($input->users[$i]->last_name) && isset($input->users[$i]->money) && isset($input->users[$i]->password)) {
                    $first_name = $input->users[$i]->first_name;
                    $last_name = $input->users[$i]->last_name;
                    $money = $input->users[$i]->money;
                    $password = $input->users[$i]->password;
                } else return false;


                $user = new User(null, $first_name, $last_name, $money, $password);
                array_push($output, $user);
            }
            return $output;
        } else return false;
    }
}

function isFile($fileIn)
{
    if ($fileIn==null) return false;
    $fileIn = file_get_contents($fileIn);
    $fileIn = json_decode($fileIn);
    if (json_last_error() == JSON_ERROR_NONE)
        return $fileIn;
    else return false;
}