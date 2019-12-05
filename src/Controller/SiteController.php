<?php
namespace App\Controller;

use App\Controller;
use hiqdev\composer\config\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Di\Container;

class SiteController extends Controller
{
    protected function getId(): string
    {
        return 'site';
    }

    public function index(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $output = $this->render('index');

        $response->getBody()->write($output);
        return $response;
    }

    public function testParameter(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('You are at test with param ' . $id);
        return $response;
    }

    public function runPerformanceTest(ServerRequestInterface $request): ResponseInterface
    {
        $isTest = $request->getUri()->getPath() === '/test';
        if ($isTest) {
            $steps = $loop = 1000;
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
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(
            "<div style='white-space: pre'>Steps: {$steps};"
            . "\n  T1: {$s1t} (OOP Configs + Autowiring)"
            . "\n  T2: {$s2t} (Arrays)"
            . "\n  T3: {$s3t} (OOP Configs + Factory)"
            . "\nDelta(T1-T2): " . round(1000 * ($s1t - $s2t), 3) . " ms"
            . "\nDelta(T3-T2): " . round(1000 * ($s3t - $s2t), 3) . " ms"
            . "\nMultiplier(T1-T2): x" . round($s1t / $s2t, 2)
            . "\nMultiplier(T3-T2): x" . round($s3t / $s2t, 2)
            . "\nAvg overhead(T1-T2): " . round(1000 * ($s1t - $s2t) / $steps, 4) . ' ms'
            . "\nAvg overhead(T3-T2): " . round(1000 * ($s3t - $s2t) / $steps, 4) . ' ms'
            . '</div>'
        );

        return $response;
    }
}
