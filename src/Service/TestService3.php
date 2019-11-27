<?php

namespace App\Service;

use Yiisoft\Yii\Cycle\CycleCommonConfig;
use Yiisoft\Yii\Cycle\CycleDbalConfig;
use Yiisoft\Yii\Cycle\CycleMigrationConfig;

class TestService3
{
    /**
     * @var CycleDbalConfig
     */
    private $dbal;
    /**
     * @var CycleCommonConfig
     */
    private $common;
    /**
     * @var CycleMigrationConfig
     */
    private $migrations;
    public function __construct(CycleDbalConfig $dbal, CycleCommonConfig $common, CycleMigrationConfig $migrations)
    {

        $this->dbal = $dbal;
        $this->common = $common;
        $this->migrations = $migrations;
    }
}
