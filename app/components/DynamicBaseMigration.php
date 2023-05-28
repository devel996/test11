<?php

declare(strict_types=1);

namespace app\components;

use yii\db\Connection;
use yii\db\Migration;
use yii\di\Instance;

abstract class DynamicBaseMigration extends Migration
{
    public function init()
    {
        parent::init();
        $this->setGlobalDbName();
        $this->db = Instance::ensure($this->db, Connection::class);
        $this->db->getSchema()->refresh();
        $this->db->enableSlaves = false;
    }

    private function setGlobalDbName(): void
    {
        $name = explode('dbname=', $this->db->dsn)[1] ?? ConnectionName::getOriginalDatabaseName();
        $dbConnectionName = $name === ConnectionName::getTestDatabaseName() ? ConnectionName::DB_TEST : ConnectionName::DB;
        ConnectionName::set($dbConnectionName);
    }
}