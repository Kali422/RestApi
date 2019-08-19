<?php


namespace Api\service;


use Api\model\UserFactory;
use Api\model\UserRepository;
use Api\view\UserView;

class UserController
{
    public $fileIn = "php://input";
    /**
     * @var \SQLite3
     */
    private $dbHandle;
    /**
     * @var string
     */
    private $request_method;
    /**
     * @var int
     */
    private $userId;

    public function __construct(\SQLite3 $dbHandle, string $request_method, $userId)
    {
        $this->dbHandle = $dbHandle;
        $this->request_method = $request_method;
        $this->userId = $userId;
    }

    public function processRequest()
    {
        switch ($this->request_method) {


            case "GET":
                if ($this->userId) {
                    $user = UserRepository::readUser($this->dbHandle, $this->userId);
                    if ($user) {
                        http_response_code(200);
                        return json_encode(UserView::renderOneUser($user));
                    } else {
                        http_response_code(404);
                        return json_encode(['message' => 'user not found']);
                    }
                } else {
                    $allUsers = UserRepository::readAllUsers($this->dbHandle);
                    if (count($allUsers) > 0) {
                        http_response_code(200);
                        return json_encode(UserView::renderAllUsers($allUsers));
                    } else {
                        http_response_code(400);
                        return json_encode(['message' => '0 users in database']);
                    }
                }
                break;


            case "POST":
                if ($users = UserFactory::createUsersFromRequest($this->fileIn)) {
                    http_response_code(201);
                    $messages = UserRepository::insertUsers($this->dbHandle, $users);
                    return json_encode($messages);
                } else {
                    http_response_code(400);
                    return json_encode(["message" => "wrong input"]);
                }
                break;


            case "PUT":

                if ($this->userId) {
                    if ($user = UserFactory::createUsersFromRequest($this->fileIn))
                        if (UserRepository::updateUser($this->dbHandle, $user[0], $this->userId)) {

                            http_response_code(200);
                            return json_encode(['message' => 'user updated']);
                        } else {
                            http_response_code(400);
                            return json_encode(['message' => 'user not updated']);
                        }
                    else {
                        http_response_code(400);
                        return json_encode(['message' => 'user not updated']);
                    }
                } else {
                    http_response_code(404);
                    return json_encode(['message' => 'set id to update']);
                }

                break;


            case "DELETE":
                if ($this->userId) {
                    if (UserRepository::deleteUser($this->dbHandle, $this->userId)) {
                        http_response_code(204);
                        return json_encode(['message' => 'user with id ' . $this->userId . ' deleted']);
                    }
                    else {
                        http_response_code(400);
                        return json_encode(['message' => 'user with id ' . $this->userId . ' not deleted']);
                    }
                }
                else {
                    http_response_code(404);
                    return json_encode(['message' => 'set id to delete']);
                }
                break;


            default:
                http_response_code(404);
                return json_encode(['message' => 'wrong request type']);
                break;
        }
    }

}