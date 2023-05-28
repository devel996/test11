<?php

declare(strict_types=1);

namespace api\auth\v2;

use ApiTester;
use app\modules\auth\v2\domain\entities\accessToken\AuthenticateAccessToken;
use app\modules\auth\v2\fixtures\AccessTokenFixture;
use app\modules\auth\v2\fixtures\UserFixture;

final class AuthenticateCest
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

    public function tryToSuccessfulAuthenticateAsUserTwoFa(ApiTester $I): void
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

        $accessToken = AuthenticateAccessToken::findOne(
            [
                'user__id' => $user['id'],
                'refresh_token' => null,
                'access_token' => $token['token']
            ]
        );

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token']);

        $I->sendPost('/api/v2/auth/authenticate', [
            'email' => $user['email'],
            'auth_code' => $accessToken->auth_code
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('refresh_token');
    }

    public function tryToAuthenticateAsUserTwoFaWithWrongAuthCode(ApiTester $I): void
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

        $accessToken = AuthenticateAccessToken::findOne(
            [
                'user__id' => $user['id'],
                'refresh_token' => null,
                'access_token' => $token['token']
            ]
        );

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token']);

        $I->sendPost('/api/v2/auth/authenticate', [
            'email' => $user['email'],
            'auth_code' => $accessToken->auth_code - 100
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":["Incorrect Auth Code');
    }

    public function tryToAuthenticateAsUserTwoFaWithNotExistingEmail(ApiTester $I): void
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

        $accessToken = AuthenticateAccessToken::findOne(
            [
                'user__id' => $user['id'],
                'refresh_token' => null,
                'access_token' => $token['token']
            ]
        );

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token']);

        $I->sendPost('/api/v2/auth/authenticate', [
            'email' => 'wrong@test.local',
            'auth_code' => $accessToken->auth_code
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":["Email or auth code are incorrect');
    }

    public function tryToAuthenticateAsUserTwoFaWithExistingWrongEmail(ApiTester $I): void
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

        $accessToken = AuthenticateAccessToken::findOne(
            [
                'user__id' => $user['id'],
                'refresh_token' => null,
                'access_token' => $token['token']
            ]
        );

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token['token']);

        $I->sendPost('/api/v2/auth/authenticate', [
            'email' => $this->users['deletedUser']['email'],
            'auth_code' => $accessToken->auth_code
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":["Incorrect Email');
    }

    public function tryToAuthenticateAsUserTwoFaWithWrongBearerToken(ApiTester $I): void
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

        $accessToken = AuthenticateAccessToken::findOne(
            [
                'user__id' => $user['id'],
                'refresh_token' => null,
                'access_token' => $token['token']
            ]
        );

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer wrongtoken');

        $I->sendPost('/api/v2/auth/authenticate', [
            'email' => $user['email'],
            'auth_code' => $accessToken->auth_code
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":"Your request was made with invalid credentials.');
    }
}
