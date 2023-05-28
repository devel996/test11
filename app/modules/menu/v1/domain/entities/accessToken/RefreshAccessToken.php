<?php

declare(strict_types=1);

namespace app\modules\auth\v2\domain\entities\accessToken;

class RefreshAccessToken extends AbstractAccessToken
{
    /**
     * @return list<array>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(),[
            [['access_token', 'refresh_token', 'ip'], 'required'],
        ]);
    }
}
