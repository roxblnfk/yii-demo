<?php
namespace App\Factory;

use App\CycleCommonConfig3;
use App\CycleDbalConfig3;
use App\CycleMigrationConfig3;
use App\Service\TestService3;
use Psr\Container\ContainerInterface;

class Service3Factory
{
    public function __invoke(ContainerInterface $container)
    {
        $Dbal = $container->get(CycleDbalConfig3::class);
        $Common = $container->get(CycleCommonConfig3::class);
        $Migration = $container->get(CycleMigrationConfig3::class);
        return new TestService3($Dbal, $Common, $Migration);
    }
}
