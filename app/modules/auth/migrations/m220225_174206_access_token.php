<?php

declare(strict_types=1);

use app\components\DynamicBaseMigration as Migration;
use app\modules\auth\models\AccessToken;
use app\models\User;

/**
 * Class m220225_174206_access_token
 */
class m220225_174206_access_token extends Migration
{
    public function safeUp(): void
    {
        $this->createTable(
            'access_token',
            [
            'id' => $this->primaryKey(),
            'refresh_token' => $this->string(),
            'access_token' => $this->string(),
            'auth_code' => $this->integer()->null(),
            'user__id' => $this->integer()->notNull(),
            'ip' => $this->string(15),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp(),
            ]
        );

        $this->createIndex(
            'idx_unique_refresh_token_access_token_user__id_ip',
            'access_token',
            ['refresh_token', 'access_token', 'user__id', 'ip'],
            true
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('access_token');
    }
}
