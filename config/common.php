<?php

use App\CycleCommonConfig3;
use App\CycleDbalConfig3;
use App\CycleMigrationConfig3;
use App\Factory\LoggerFactory;
use App\Factory\MailerFactory;
use App\Factory\Service2Factory;
use App\Factory\Service3Factory;
use App\Service\TestService2;
use App\Service\TestService3;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Factory\Definitions\Reference;
use Yiisoft\Log\Target\File\FileRotator;
use Yiisoft\Log\Target\File\FileRotatorInterface;
use Yiisoft\Mailer\MailerInterface;


/**
 * @var array $params
 */

return [
    Aliases::class => $params['aliases'],
    Psr\SimpleCache\CacheInterface::class => ArrayCache::class,
    CacheInterface::class => Cache::class,
    LoggerInterface::class => new LoggerFactory(),
    FileRotatorInterface::class => [
        '__class' => FileRotator::class,
        '__construct()' => [
            10
        ]
    ],
    \Swift_Transport::class => \Swift_SmtpTransport::class,
    \Swift_SmtpTransport::class => [
        '__class' => \Swift_SmtpTransport::class,
        '__construct()' => [
            'host' => $params['mailer']['host'],
            'port' => $params['mailer']['port'],
            'encryption' => $params['mailer']['encryption'],
        ],
        'setUsername()' => [$params['mailer']['username']],
        'setPassword()' => [$params['mailer']['password']],
    ],
    MailerInterface::class => new MailerFactory(),

    TestService2::class => new Service2Factory($params['cycle.dbal'], $params['cycle.common'], $params['cycle.migrations']),
    # TEST 3
    //*
    TestService3::class => new Service3Factory(),
    /*/
    TestService3::class => [
        '__class' => TestService3::class,
        '__construct' => [
            Reference::to(CycleDbalConfig3::class),
            Reference::to(CycleCommonConfig3::class),
            Reference::to(CycleMigrationConfig3::class)
        ]
    ],//*/
    CycleCommonConfig3::class => [
        '__class' => CycleCommonConfig3::class,
        '__construct' => []
    ],
    CycleDbalConfig3::class => [
        '__class' => CycleDbalConfig3::class,
        '__construct' => [Reference::to(Aliases::class)]
    ],
    CycleMigrationConfig3::class => [
        '__class' => CycleMigrationConfig3::class,
        '__construct' => [Reference::to(Aliases::class)]
    ],
];
