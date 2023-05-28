<?php

declare(strict_types=1);

namespace api\auth\v2;

use ApiTester;
use app\modules\auth\v2\fixtures\AccessTokenFixture;
use app\modules\auth\v2\fixtures\UserFixture;

final class LoginCest
{
    private string $password = 'admin';
    private array $users;
    
    public function __construct()
    {
        $this->users = UserFixture::getArrayData();
    }

    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class,
            'accessTokens' => AccessTokenFixture::class
        ];
    }

    public function tryToLoginViaEmailAndPasswordAsUserNoTwoFa(ApiTester $I): void
    {
        $user = $this->users['activeConfirmedUserWithout2fa'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email' => $user['email'],
            'password' => $this->password
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('refresh_token');
    }

    public function tryToLoginViaEmailAndPasswordAsUserTwoFa(ApiTester $I): void
    {
        $user = $this->users['activeConfirmedUserWith2fa'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email' => $user['email'],
            'password' => $this->password
        ]);

        $I->seeResponseCodeIs(202);
        $I->seeResponseIsJson();
        $I->seeResponseContains('is_two_fa_active');
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('email":"' . $user['email']);
    }

    public function tryToLoginViaEmailAndPasswordAsNotActiveUser(ApiTester $I): void
    {
        $user = $this->users['notActiveUser'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email' => $user['email'],
            'password' => $this->password
        ]);

        $I->canSeeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message');
        $I->seeResponseContains('Invalid User');
    }

    public function tryToLoginViaEmailAndPasswordAsNotConfirmedUser(ApiTester $I): void
    {
        $user = $this->users['notConfirmedUser'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email' => $user['email'],
            'password' => $this->password
        ]);

        $I->canSeeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message');
        $I->seeResponseContains('Invalid User');
    }

    public function tryToLoginViaEmailAndPasswordAsDeletedUser(ApiTester $I): void
    {
        $user = $this->users['deletedUser'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email' => $user['email'],
            'password' => $this->password
        ]);

        $I->canSeeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message');
        $I->seeResponseContains('Invalid User');
    }

    public function tryToLoginViaEmailAndPasswordAsNotCorrectUser(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email' => 'notcorrect@gmail',
            'password' => '111'
        ]);

        $I->canSeeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('email":["Incorrect value');
    }

    public function tryToLoginWithoutPassword(ApiTester $I): void
    {
        $user = $this->users['activeConfirmedUserWithout2fa'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email' => $user['email']
        ]);

        $I->canSeeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message');
        $I->seeResponseContains('Email or Password are incorrect');
    }

    public function tryToLoginWithMixedWrongData(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/login', [
            'email1' => 'someemail@gmail.com',
            'password1' => '111'
        ]);

        $I->canSeeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message');
        $I->seeResponseContains('Email or Password are incorrect');
    }
}
