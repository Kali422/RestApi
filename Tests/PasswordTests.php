<?php


namespace Api\Tests;


use Api\model\User;
use PHPUnit\Framework\TestCase;

class PasswordTests extends TestCase
{
    /**
     * @dataProvider provider
     */
    function testSetPassword($password,$expected)
    {
        $user = new User(null, 'asdas','fdsaf',100, null);
        $result=$user->setPassword($password);
        self::assertEquals($result,$expected);
    }

    function provider()
    {
        return [
            ['haslo' , false],
            ['jdasijnk',false],
            ['jdaS@jnk',true],
            ['jdaS@jn',false],
            ['jdaS0jnk',false],
            ['jdaa@jnk',false],
            ['uijiSjcuhsd@',true],
            ['@@Wabc',false],
            ['qwertyui',false],
            ['pask@n nA',true],
            ['          ',false],
            ['@@@@@@@@@@@@@@@@@@@@2',false],
            ['AAAAAAAAAAAAAAAAAAaa',false],
            ['LA@KSADSKLADKALF@@asfkalsdka',true]
        ];
    }

}