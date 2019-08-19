<?php

namespace Api\Tests;

use Api\config\Database;
use Api\service\UserController;
use PHPUnit\Framework\TestCase;

class ProcessRequestTests extends TestCase
{
    private $userController;
    private $dbHandle;
    private $inputPostTrue = "/var/www/html/Api/Tests/inputPostTrue";
    private $inputPutTrue = "/var/www/html/Api/Tests/inputPutTrue";
    private $inputPostFalse = "/var/www/html/Api/Tests/inputPostFalse";

    function setUp()
    {
        $this->dbHandle = (new Database())->getConnection();
    }


    /**
     * @dataProvider providerResponseCodes
     * @param $requestMethod
     * @param null $userId
     * @param null $inputBody
     * @param $expectedHttpResponse
     */
    function testResponseCodes($requestMethod, $userId = null, $inputBody = null, $expectedHttpResponse)
    {
        $this->userController = new UserController($this->dbHandle, $requestMethod, $userId);
        if (isset($inputBody)) {
            $this->userController->fileIn = $inputBody;
        }
        $this->userController->processRequest();
        self::assertEquals($expectedHttpResponse, http_response_code());

    }


    function providerResponseCodes()
    {

        return [
            ["POST", null, $this->inputPostTrue, 201],
            ["PUT", 50, $this->inputPutTrue, 200],
            ["POST", null, $this->inputPostFalse, 400],
            ["PUT", 900, $this->inputPutTrue, 400],
            ["PUT", null, $this->inputPutTrue, 404],
            ["POST", null, null, 400],
            ["GET", null, null, 200],
            ["GET", 20, null, 200],
            ["GET", 1000, null, 404],
            ["DELETE", 11, null, 400],
            ["DELETE", null, null, 404],
            ["DELETE", 1000, null, 400],
            ['TEST', null, null, 404]
        ];


    }


    /**
     * @param $requestMethod
     * @param null $userId
     * @param null $inputBody
     * @param $expectedMessage
     * @dataProvider providerMessages
     */
    function testMessages($requestMethod, $userId = null, $inputBody = null, $expectedMessage)
    {
        $this->userController = new UserController($this->dbHandle, $requestMethod, $userId);
        if (isset($inputBody)) {
            $this->userController->fileIn = $inputBody;
        }
        $result = $this->userController->processRequest();
        self::assertEquals(json_encode($expectedMessage), $result);
    }

    function providerMessages()
    {
        $output['users'] = array();
        array_push($output['users'], ['id' => 25, 'first_name' => 'qfsafads', 'last_name' => 'fsaf', 'money' => 1231321.00, 'password'=>'j@sdWlzm']);
        $output2['users'] = array();
        array_push($output2['users'], ['id' => 24, 'first_name' => 'qfsafads', 'last_name' => 'fsaf', 'money' => 1231321.00, 'password'=>"j@sdWlzm"]);
        return
            [
                ["GET", 1, null, ['message' => 'user not found']],
                ["POST", null, $this->inputPostFalse, ['message' => 'wrong input']],
                ["POST", null, $this->inputPostTrue, ['message' => array('user qfsafads created')]],
                ["PUT", 51, $this->inputPutTrue, ['message' => 'user updated']],
                ["PUT", 51, $this->inputPostFalse, ['message' => 'user not updated']],
                ["PUT", null, $this->inputPutTrue, ['message' => 'set id to update']],
                ["DELETE", 70, null, ['message' => 'user with id 70 deleted']],
                ["DELETE", 70, null, ['message' => 'user with id 70 not deleted']],
                ["DELETE", null, null, ['message' => 'set id to delete']],
                ["TEST", null, null, ['message' => 'wrong request type']],
                ["GET", 25, null, $output],
                ["GET",24,null,$output2]

            ];
    }


    /**
     * @param $userId
     * @param $expected
     * @dataProvider providerWasUserDeleted
     */
    function testWasUserDeleted($userId, $expected)
    {
        $this->userController = new UserController($this->dbHandle, "DELETE", $userId);
        $deleteResult = $this->userController->processRequest();
        $this->userController = new UserController($this->dbHandle, "GET", $userId);
        $getResult = $this->userController->processRequest();
        if ($expected == true) {
            self::assertEquals(json_encode(['message' => 'user with id ' . $userId . ' deleted']), $deleteResult);
            self::assertEquals(json_encode(['message' => 'user not found']), $getResult);
        } else {
            self::assertEquals(json_encode(['message' => 'user with id ' . $userId . ' not deleted']), $deleteResult);
        }
    }

    function providerWasUserDeleted()
    {
        return [
            [1, false],
            [2, false],
            [13, true],
            [14, true]
        ];
    }


    /**
     * @param $userId
     * @param $inputBody
     * @param $expected
     * @dataProvider providerWasUserUpdated
     */
    function testWasUserUpdated($userId, $inputBody, $expected)
    {
        $this->userController = new UserController($this->dbHandle, "PUT", $userId);
        $this->userController->fileIn = $inputBody;
        $putResult = $this->userController->processRequest();
        $this->userController = new UserController($this->dbHandle, "GET", $userId);
        $getResult = $this->userController->processRequest();
        if ($expected == true) {
            $input = json_decode(file_get_contents($inputBody));
            $getResult = json_decode($getResult);
            unset($getResult->users[0]->id);
            self::assertEquals(json_encode(['message' => 'user updated']), $putResult);
            self::assertEquals($input, $getResult);
        } else {
            self::assertEquals(json_encode(['message' => 'user not updated']), $putResult);
        }
    }

    function providerWasUserUpdated()
    {
        return [
            [75, $this->inputPutTrue, true],
            [23, $this->inputPutTrue, true],
            [1000, $this->inputPutTrue, false],
            [75, $this->inputPostFalse, false]
        ];
    }


    /**
     * @param $inputBody
     * @param $expected
     * @dataProvider providerWasUserCreated
     */
    function testWasUserCreated($inputBody, $expected)
    {
        $this->userController = new UserController($this->dbHandle, "POST", $userId = null);
        $this->userController->fileIn = $inputBody;
        $postResult = $this->userController->processRequest();
        $id = $this->dbHandle->lastInsertRowID();
        $this->userController = new UserController($this->dbHandle, "GET", $id);
        $getResult = $this->userController->processRequest();
        if ($expected == true) {
            $input = json_decode(file_get_contents($inputBody));
            $getResult = json_decode($getResult);
            unset($getResult->users[0]->id);
            self::assertEquals(json_encode(['message' => array('user ' . $input->users[0]->first_name . ' created')]), $postResult);
            self::assertEquals($input, $getResult);
        } else {
            self::assertEquals(json_encode(['message' => 'wrong input']), $postResult);
        }
    }

    function providerWasUserCreated()
    {
        return [
            [$this->inputPostTrue, true],
            [$this->inputPostFalse, false],
            [$this->inputPutTrue, true],
            [null, false]
        ];
    }


}