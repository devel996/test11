<?php

declare(strict_types=1);

namespace app\components;

use yii\di\Instance;
use yii\rbac\DbManager as Manager;
use Yii;
use yii\db\Connection;

class ConsoleDbManager extends Manager
{
    public function init()
    {
        $db = ConnectionName::get() === ConnectionName::DB_TEST ? Yii::$app->dbTest : Yii::$app->db;
        $this->db = Instance::ensure($db, Connection::class);

        parent::init();
    }
}