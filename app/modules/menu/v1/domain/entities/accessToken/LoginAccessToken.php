<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\entities\accessToken;

class LoginAccessToken extends AbstractAccessToken
{
    /**
     * @return list<array>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(),[
            [['user__id', 'auth_code', 'access_token'], 'required'],
        ]);
    }
}
