<?php

namespace App\Service;

class TestService2
{
    /**
     * @var array
     */
    private $dbal;
    /**
     * @var array
     */
    private $common;
    /**
     * @var array
     */
    private $migrations;
    public function __construct($dbal, $common, $migrations)
    {

        $this->dbal = $dbal;
        $this->common = $common;
        $this->migrations = $migrations;
    }
}
