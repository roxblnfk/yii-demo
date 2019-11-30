<?php
/**
 * @var Goridge\RelayInterface $relay
 */
use Spiral\Goridge;
use Spiral\RoadRunner;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Web\Application;
use hiqdev\composer\config\Builder;

ini_set('display_errors', 'stderr');
require 'vendor/autoload.php';

$worker = new RoadRunner\Worker(new Goridge\StreamRelay(STDIN, STDOUT));
$psr7 = new RoadRunner\PSR7Client($worker);

// Don't do it in production, assembling takes it's time
Builder::rebuild();

while ($request = $psr7->acceptRequest()) {
    $steps = $loop = 10000;
    $s3t = $s2t = $s1t = 0;
    do {
        $container = new Container(require Builder::path('web'));
        $m1 = microtime(true);
        $service1 = $container->get(\App\Service\TestService1::class);
        $m2 = microtime(true);
        $service2 = $container->get(\App\Service\TestService2::class);
        $m3 = microtime(true);
        $service3 = $container->get(\App\Service\TestService3::class);
        $m4 = microtime(true);
        $s1t += $m2 - $m1;
        $s2t += $m3 - $m2;
        $s3t += $m4 - $m3;
        unset($container, $service1, $service2);
    } while (--$loop);

    try {
        $container = new Container(require Builder::path('web'));
        $container->set(Spiral\RoadRunner\PSR7Client::class, $psr7);
        $container->set(\Yiisoft\Yii\Web\Emitter\EmitterInterface::class, \App\Emitter\RoadrunnerEmitter::class);

        $response = new \Nyholm\Psr7\Response();
        $response->getBody()->write("Steps: {$steps};"
            . "\n  T1: {$s1t} (OOP Configs + Autowiring)"
            . "\n  T2: {$s2t} (Arrays)"
            . "\n  T3: {$s3t} (OOP Configs + Factory)"
            . "\nDelta(T1-T2): " . round(1000 * ($s1t - $s2t), 3) . " ms"
            . "\nDelta(T3-T2): " . round(1000 * ($s3t - $s2t), 3) . " ms"
            . "\nMultiplier(T1-T2): x" . round($s1t / $s2t, 2)
            . "\nMultiplier(T3-T2): x" . round($s3t / $s2t, 2)
            . "\nAvg overhead(T1-T2): " . round(1000 * ($s1t - $s2t) / $steps, 4) . ' ms'
            . "\nAvg overhead(T3-T2): " . round(1000 * ($s3t - $s2t) / $steps, 4) . ' ms');
        $emitter = $container->get(\Yiisoft\Yii\Web\Emitter\EmitterInterface::class);
        $emitter->emit($response);
        // $container->get(Application::class)->handle($request);
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);
    }
    unset($container);
}
