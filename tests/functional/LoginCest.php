<?php

class LoginCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Сектор', 'title');

    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('form', []);
        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('form', [
            'username' => 'admin',
            'password' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('form', [
            'login' => 'admin',
            'password' => 'admin',
        ]);
        $I->see('Logout (admin)');
        $I->dontSeeElement('form#login-form');
    }
}