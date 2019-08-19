<?php


namespace Api\model;


class UserRepository
{

    public static function insertUsers(\SQLite3 $databaseHandle, array $users)
    {
        $output['message'] = array();
        $num = count($users);
        for ($i = 0; $i < $num; $i++) {
            if (self::insertUser($databaseHandle, $users[$i])) {
                array_push($output['message'], 'user ' . $users[$i]->getFirstName() . ' created');
            } else {
                http_response_code(400);
                array_push($output['message'], 'user ' . $users[$i]->getFirstName() . ' not created');
            }
        }
        return $output;
    }

    public static function insertUser(\SQLite3 $databaseHandle, User $user): string
    {
        $first_name = $user->getFirstName();
        $last_name = $user->getLastName();
        $money = $user->getMoney();
        $password = $user->getPassword();
        $query = <<<"SQL"
insert into users (first_name, last_name, money, password) VALUES ("$first_name","$last_name","$money", "$password")
SQL;

        if ($password != null)
            if ($databaseHandle->query($query))
                return true;
            else return false;
        else return false;

    }

    public static function updateUser(\SQLite3 $databaseHandle, User $user, int $idToUpdate): string
    {

        $first_name = $user->getFirstName();
        $last_name = $user->getLastName();
        $money = $user->getMoney();
        $password = $user->getPassword();

        if (($databaseHandle->querySingle("select * from users where id=$idToUpdate")) == null || false==isset($password)) {
            return false;
        }

        $query = <<<"SQL"
update users set first_name="$first_name", last_name="$last_name", money="$money", password="$password"
where id=$idToUpdate
SQL;

        if ($databaseHandle->query($query)) {
            return true;
        } else return false;

    }

    public static function deleteUser(\SQLite3 $databaseHandle, int $idToDelete): string
    {
        $query = <<<SQL
delete from users where id = $idToDelete
SQL;

        if (($databaseHandle->querySingle("select * from users where id=$idToDelete")) == null) {
            return false;
        } elseif ($databaseHandle->query($query))
            return true;
        else return false;
    }

    public static function readUser(\SQLite3 $databaseHandle, int $idToRead)
    {
        $query = <<<"SQL"
select * from users where id=$idToRead
SQL;

        if (null != $databaseHandle->querySingle($query)) {
            $result = $databaseHandle->query($query);
            $row = $result->fetchArray();
            $id = $row['id'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $money = $row['money'];
            $password = $row['password'];

            return new User($id, $first_name, $last_name, $money, $password);
        } else return false;

    }

    public static function readAllUsers(\SQLite3 $dbHandle)
    {
        $query = <<<"SQL"
select * from users
SQL;

        $users = array();

        $result = $dbHandle->query($query);

        while ($row = $result->fetchArray()) {
            $user = new User($row['id'], $row["first_name"], $row["last_name"], $row["money"], $row['password']);
            array_push($users, $user);
        }

        return $users;
    }
}