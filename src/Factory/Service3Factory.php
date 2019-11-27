<?php
namespace App\Factory;

use App\Service\TestService3;
use Psr\Container\ContainerInterface;
use Yiisoft\Yii\Cycle\CycleCommonConfig;
use Yiisoft\Yii\Cycle\CycleDbalConfig;
use Yiisoft\Yii\Cycle\CycleMigrationConfig;

class Service3Factory
{
    public function __invoke(ContainerInterface $container)
    {
        $Dbal = $container->get(CycleDbalConfig::class);
        $Common = $container->get(CycleCommonConfig::class);
        $Migration = $container->get(CycleMigrationConfig::class);
        return new TestService3($Dbal, $Common, $Migration);
    }
}
