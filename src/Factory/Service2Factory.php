<?php
namespace App\Factory;

use App\Service\TestService2;
use Psr\Container\ContainerInterface;

class Service2Factory
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
    public function __construct(array $dbal, array $common, array $migrations) {

        $this->dbal = $dbal;
        $this->common = $common;
        $this->migrations = $migrations;
    }
    public function __invoke(ContainerInterface $container)
    {
        return new TestService2($this->dbal, $this->common, $this->migrations);
    }
}
