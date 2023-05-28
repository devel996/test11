<?php

declare(strict_types=1);

namespace api\auth\v2;

use ApiTester;
use app\modules\auth\v2\fixtures\AccessTokenFixture;
use app\modules\auth\v2\fixtures\UserFixture;

final class ResendAuthCodeCest
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

    public function tryToSuccessfulResendAuthCodeAsUserTwoFa(ApiTester $I): void
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
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('is_two_fa_active":true');
        $I->seeResponseContains('email":"' . $user['email']);
        $token = $I->grabDataFromResponseByJsonPath('access_token')[0];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token']);

        $I->sendPost('/api/v2/auth/resend-auth-code', [
            'email' => $user['email']
        ]);

        $I->seeResponseCodeIs(202);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('is_two_fa_active":true');
        $I->seeResponseContains('email":"' . $user['email']);
    }

    public function tryToUnSuccessfulResendAuthCodeAsUserTwoFaWrongToken(ApiTester $I): void
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
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('is_two_fa_active":true');
        $I->seeResponseContains('email":"' . $user['email']);
        $token = $I->grabDataFromResponseByJsonPath('access_token')[0];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token'] . 'wrong');

        $I->sendPost('/api/v2/auth/resend-auth-code', [
            'email' => $user['email']
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":"Your request was made with invalid credentials.');
    }

    public function tryToUnSuccessfulResendAuthCodeAsUserTwoFaWrongEmail(ApiTester $I): void
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
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('is_two_fa_active":true');
        $I->seeResponseContains('email":"' . $user['email']);
        $token = $I->grabDataFromResponseByJsonPath('access_token')[0];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token']);

        $I->sendPost('/api/v2/auth/resend-auth-code', [
            'email' => 'wrong@gmail.com'
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('email":["Invalid data"');
    }

    public function tryToUnSuccessfulResendAuthCodeAsUserTwoFaWithoutEmail(ApiTester $I): void
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
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('is_two_fa_active":true');
        $I->seeResponseContains('email":"' . $user['email']);
        $token = $I->grabDataFromResponseByJsonPath('access_token')[0];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token']);

        $I->sendPost('/api/v2/auth/resend-auth-code', []);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('email":["Incorrect value');
    }
}
