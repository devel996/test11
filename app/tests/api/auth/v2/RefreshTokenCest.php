<?php

declare(strict_types=1);

namespace api\auth\v2;

use ApiTester;
use app\modules\auth\v2\domain\entities\accessToken\AuthenticateAccessToken;
use app\modules\auth\v2\fixtures\AccessTokenFixture;
use app\modules\auth\v2\fixtures\UserFixture;

final class RefreshTokenCest
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

    public function tryToSuccessfulRefreshTokenAsUserTwoFa(ApiTester $I): void
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
        $newAccessToken = $I->grabDataFromResponseByJsonPath('access_token')[0]['token'];
        $newRefreshToken = $I->grabDataFromResponseByJsonPath('refresh_token')[0]['token'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/refresh-token', [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('refresh_token');
    }

    public function tryToSuccessfulRefreshTokenAsUserNotTwoFa(ApiTester $I): void
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
        $newAccessToken = $I->grabDataFromResponseByJsonPath('access_token')[0]['token'];
        $newRefreshToken = $I->grabDataFromResponseByJsonPath('refresh_token')[0]['token'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/refresh-token', [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('refresh_token');
    }

    public function tryToUnSuccessfulRefreshTokenAsUserNotTwoFa(ApiTester $I): void
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
        $newAccessToken = $I->grabDataFromResponseByJsonPath('access_token')[0]['token'];
        $newRefreshToken = $I->grabDataFromResponseByJsonPath('refresh_token')[0]['token'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/refresh-token', [
            'access_token' => $newAccessToken . 'wrong',
            'refresh_token' => $newRefreshToken
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('refresh_token":["Invalid user token');
    }

    public function tryToUnSuccessfulRefreshTokenAsUserNotTwoFaWithoutAccessToken(ApiTester $I): void
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
        $newRefreshToken = $I->grabDataFromResponseByJsonPath('refresh_token')[0]['token'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/refresh-token', [
            'refresh_token' => $newRefreshToken
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":["Something went wrong!');
    }

    public function tryToUnSuccessfulRefreshTokenAsUserNotTwoFaWithoutRefreshToken(ApiTester $I): void
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
        $newAccessToken = $I->grabDataFromResponseByJsonPath('access_token')[0]['token'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/refresh-token', [
            'access_token' => $newAccessToken
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":["Something went wrong!');
    }

    public function tryToSuccessfulRefreshTokenAsUserNotTwoFaUsingBearerToken(ApiTester $I): void
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
        $newAccessToken = $I->grabDataFromResponseByJsonPath('access_token')[0]['token'];
        $newRefreshToken = $I->grabDataFromResponseByJsonPath('refresh_token')[0]['token'];

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $newAccessToken);

        $I->sendPost('/api/v2/auth/refresh-token', [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('access_token');
        $I->seeResponseContains('refresh_token');
    }
}
