<?php

declare(strict_types=1);

namespace app\modules\auth;

use yii\base\Module;

/**
 * auth module definition class
 */
class AuthModule extends Module
{
    /**
     * @phpstan-ignore-next-line
     */
    public $controllerNamespace = 'app\modules\auth\controllers';
}
