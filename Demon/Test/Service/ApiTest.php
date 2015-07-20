<?php
namespace Demon\Test\Service;

use Demon\Service\Api;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{

    public function testUserServiceRegister1OK()
    {
        //create first valid user
        $user = Api::request('user', 'register', ['login' => 'test1', 'password' => 'test222']);
        $this->assertTrue($user['success']);
    }

    public function testUserServiceRegister2OK()
    {
        //create second valid user
        $user = Api::request('user', 'register', ['login' => 'test2', 'password' => 'test222']);
        $this->assertTrue($user['success']);
    }

    /**
     * @expectedException \Demon\Exception\ApiException
     */
    public function testUserServiceRegisterNoFields()
    {
        //create user without required fields
        $user3 = Api::request('user', 'register', []);
    }

    /**
     * @depends testUserServiceRegister1OK
     */
    public function testUserServiceAuthWrongPass()
    {
        //user found, password wrong
        $ret = Api::request('user', 'auth', ['login' => 'test1', 'password' => 'wrong']);
        $this->assertFalse($ret['success']);
    }

    /**
     * @depends testUserServiceRegister1OK
     * @expectedException \Demon\Exception\DataNotFoundException
     */
    public function testUserServiceAuthWrongLogin()
    {
        //user not found
        $ret = Api::request('user', 'auth', ['login' => 'wrong', 'password' => 'wrong']);
    }

    /**
     * @depends testUserServiceRegister1OK
     */
    public function testUserServiceAuthOK()
    {
        //user found, password correct
        $ret = Api::request('user', 'auth', ['login' => 'test1', 'password' => 'test222']);
        $this->assertTrue($ret['success']);
        $this->assertInternalType('string', $ret['token']);

        return $ret['token'];
    }

    /**
     * @depends testUserServiceAuthOK
     * @expectedException \Demon\Exception\DataNotFoundException
     */
    public function testUserServiceGetUserWrongToken($token)
    {
        $data = Api::request('user', 'getuser', ['token' => 'wrong']);
    }

    /**
     * @depends testUserServiceAuthOK
     * @expectedException \Demon\Exception\ApiException
     */
    public function testUserServiceGetUserNoToken($token)
    {
        $data = Api::request('user', 'getuser', []);
    }

    /**
     * @depends testUserServiceAuthOK
     */
    public function testUserServiceGetUserOK($token)
    {
        $user = Api::request('user', 'getuser', ['token' => $token]);
        $this->assertEquals(1, $user['ID']);
        $this->assertEmpty($user['heroes']);
    }

    /**
     * @depends testUserServiceAuthOK
     */
    public function testHeroServiceCreateOK($token)
    {
        $hero = Api::request('hero', 'create', ['token' => $token, 'name' => 'Hero-hero']);
        $this->assertEquals(1, $hero['ID']);

        return $token;
    }

    /**
     * @depends testHeroServiceCreateOK
     * @expectedException \Demon\Exception\ApiException
     */
    public function testHeroServiceChooseWrongID($token)
    {
        $hero = Api::request('hero', 'choose', ['token' => $token, 'id' => 999]);
    }

    /**
     * @depends testHeroServiceCreateOK
     */
    public function testHeroServiceChooseOK($token)
    {
        $ret = Api::request('hero', 'choose', ['token' => $token, 'id' => 1]);
        $this->assertEquals(1, $ret['hero']['ID']);

        return $token;
    }

    /**
     * @depends testHeroServiceChooseOK
     * @expectedException \Demon\Exception\CoreObjectException
     */
    public function testMapServiceEnterWrong($token)
    {
        Api::request('map', 'enter', ['token' => $token, 'node' => 'wrong']);
    }

    /**
     * @depends testHeroServiceChooseOK
     */
    public function testMapServiceEnterOK($token)
    {
        $ret = Api::request('map', 'enter', ['token' => $token, 'node' => 'start.cave']);
        $this->assertEquals('start.cave', $ret['hero']['lastNode']);

        return $token;
    }

    /**
     * @depends testMapServiceEnterOK
     */
    public function testHeroServiceSquadCreateOK($token)
    {
        $ret = Api::request('army', 'createsquad', ['token' => $token, 'character' => 1, 'troop' => 'brigand', 'number' => 25]);
        $this->assertEquals(1, $ret['modules']['army']['squads'][1]['commander']);
        $this->assertEquals('brigand', $ret['modules']['army']['squads'][1]['troop']);
        $this->assertEquals(25, $ret['modules']['army']['squads'][1]['number']);
        $this->assertEquals(25, $ret['modules']['inventory'][1]['number']);

        return $token;
    }

   /**
     * @depends testHeroServiceSquadCreateOK
     */
    public function testArmyServiceAttackWolvesOK($token)
    {
        $ret = Api::request('army', 'attackneutral', ['token' => $token, 'squads' => [1], 'objectID' => 1]);
        $this->assertEquals(1, $ret['modules']['army']['battles'][0]['winner']);

        return $token;
    }

}