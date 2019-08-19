<?php

namespace Api\config;

use SQLite3;
use SQLiteException;

class Database
{
    private $path = "/var/www/html/Api/config/api.db";

    public function getConnection()
    {
        $handle = null;
        try {
            $handle = new SQLite3($this->path, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        } catch (SQLiteException $exception) {
            echo $exception->getMessage();
        }
        return $handle;
    }
}