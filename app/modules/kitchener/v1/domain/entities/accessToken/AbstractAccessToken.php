<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\entities\accessToken;

use app\components\JWTComponent;
use app\modules\auth\v2\domain\proxies\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "access_token".
 *
 * @property string $refresh_token
 * @property string $access_token
 * @property int $auth_code
 * @property int $user__id
 * @property string $ip
 * @property string $created_at
 * @property string|Expression $updated_at
 *
 * @property User $user
 * @property int $id [bigint unsigned]
 */
abstract class AbstractAccessToken extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'access_token';
    }

    /**
     * @return list<array>
     */
    public function rules(): array
    {
        return [
            [['user__id'], 'integer'],
            [['auth_code'], 'integer'],
            [['created_at'], 'safe'],
            [0 => ['refresh_token', 'access_token'], 1 => 'string', 'max' => 255],
            [0 => 'ip', 1 => 'string', 'max' => 15],
            ['refresh_token', 'unique'],
            [0 => 'user__id', 1 => 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user__id' => 'id']],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user__id']);
    }

    public static function getLastNotUsedTokenByUserId(int $userId): ?static
    {
        /** @var static $token */
        $token = self::find()
            ->where(['refresh_token' => null])
            ->andWhere(['is not', 'access_token', new Expression('NULL')])
            ->andWhere(['user__id' => $userId])
            ->andWhere(['ip' => Yii::$app->request->userIP])
            ->one();

        if ($token instanceof static) {
            return $token;
        }

        return null;
    }

    public static function isRefreshTokenValid(AbstractAccessToken $token): bool
    {
        /**
         * @var JWTComponent $jwt
         */
        $jwt = yiiWebApp()->get('jwt');

        if (!$jwt->isValid($token->refresh_token) || ($userId = $jwt->getUserIdFromToken($token->access_token)) === null) {
            return false;
        }

        return $token->user__id === $userId;
    }
}
