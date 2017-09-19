<?php

namespace tests\models;

use Codeception\Test\Unit;
use app\models\Users;

class UserTest extends Unit
{
    public function testFindUserById()
    {
        expect_that($user = Users::findIdentity(80));
        expect($user->username)->equals('mobile');

        expect_not(Users::findIdentity(999));
    }

    public function testFindUserByAccessToken()
    {
        expect_that($user = Users::findIdentityByAccessToken('2SuQS5vH_I2euLS-TxA5leuW_bqo9874'));
        expect($user->username)->equals('mobile');

        expect_not(Users::findIdentityByAccessToken('non-existing'));
    }

    public function testFindUserByUsername()
    {
        expect_that($user = Users::findByUsername('mobile'));
        expect_not(Users::findByUsername('not-admin'));
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser()
    {
        $user = Users::findByUsername('mobile');
        expect_that($user->validateAuthKey('2SuQS5vH_I2euLS-TxA5leuW_bqo9874'));
        expect_not($user->validateAuthKey('test102key'));
    }

    public function testFindUserByUID()
    {
        expect_that($user = Users::findByUid('8ac00f77c4b3'));
        expect($user->username)->equals('mobile');

        expect_not(Users::findIdentityByAccessToken('non-existing'));

    }
}
