<?php

namespace App\Service;

use App\CycleCommonConfig3;
use App\CycleDbalConfig3;
use App\CycleMigrationConfig3;

class TestService3
{
    /**
     * @var CycleDbalConfig3
     */
    private $dbal;
    /**
     * @var CycleCommonConfig3
     */
    private $common;
    /**
     * @var CycleMigrationConfig3
     */
    private $migrations;
    public function __construct(CycleDbalConfig3 $dbal, CycleCommonConfig3 $common, CycleMigrationConfig3 $migrations)
    {

        $this->dbal = $dbal;
        $this->common = $common;
        $this->migrations = $migrations;
    }
}
