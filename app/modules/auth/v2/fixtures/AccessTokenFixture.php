<?php

declare(strict_types=1);

namespace app\modules\auth\v2\fixtures;

use app\modules\auth\v2\domain\entities\accessToken\LoginAccessToken;
use yii\test\ActiveFixture;

class AccessTokenFixture extends ActiveFixture
{
    /** @phpstan-ignore-next-line */
    public $modelClass = LoginAccessToken::class;
}