<?php

declare(strict_types=1);

namespace api\auth\v2;

use ApiTester;
use app\components\JWTComponent;
use app\models\User;
use app\modules\auth\v2\fixtures\AccessTokenFixture;
use app\modules\auth\v2\fixtures\UserFixture;

final class ResetPasswordCest
{
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

    public function tryToResetPasswordSuccessfully(ApiTester $I): void
    {
        $user = $this->users['activeConfirmedUserWithout2fa'];

        // First query send uuid to email
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/reset-password', [
            'email' => $user['email']
        ]);

        $I->seeResponseCodeIs(202);
        $I->seeResponseIsJson();
        $I->seeResponseContains('email":"' . $user['email']);

        // Second query send password to email
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $userModel = User::findOne(['email' => $user['email']]);

        $I->sendPost('/api/v2/auth/reset-password', [
            'uuid' => $userModel?->uuid
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('email":"' . $user['email']);
    }

    public function tryToResetPasswordUsingWrongEmail(ApiTester $I): void
    {
        // First query send uuid to email
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/reset-password', [
            'email' => 'wrong@gmail.com'
        ]);

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":"User not found');
    }

    public function tryToResetPasswordUsingNotCorrectEmail(ApiTester $I): void
    {
        // First query send uuid to email
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/reset-password', [
            'email' => 'wrong@'
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContains('email":["Incorrect value');
    }

    public function tryToResetPasswordUsingWrongUuid(ApiTester $I): void
    {
        // Second query send password to email
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $user = new User();
        $user->id = 1;
        $user->email = 'test@test.com';

        $token = (new JWTComponent())->generateUUIDToken($user);

        $I->sendPost('/api/v2/auth/reset-password', [
            'uuid' => $token
        ]);

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":"User not found');
    }

    public function tryToResetPasswordUsingIncorrectUuid(ApiTester $I): void
    {
        // Second query send password to email
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPost('/api/v2/auth/reset-password', [
            'uuid' => 'faecaecc.caeceac'
        ]);

        $I->seeResponseCodeIs(500);
        $I->seeResponseIsJson();
        $I->seeResponseContains('message":"The JWT string must have two dots');
    }
}
