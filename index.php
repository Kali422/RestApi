<?php

include_once "vendor/autoload.php";

use Api\config\Database;
use Api\service\UserController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);


if ($uri[2] !== 'users') {
    header("HTTP/1.1 404 Not Found");
    exit();
}


$userId = null;
if (isset($uri[3])) {
    $userId = (int)$uri[3];
}

$requestMethod = (string)$_SERVER["REQUEST_METHOD"];

$dbHandle = (new Database())->getConnection();

$controller = new UserController($dbHandle, $requestMethod, $userId);

$result= $controller->processRequest();

echo $result;