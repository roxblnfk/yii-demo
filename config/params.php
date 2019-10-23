<?php

use App\Console\Command\CreateUser;
use App\Console\Command\Migrate;

return [
    'mailer.host' => 'smtp.example.com',
    'mailer.port' => 25,
    'mailer.encryption' => null,
    'mailer.username' => 'admin@example.com',
    'mailer.password' => '',

    'supportEmail' => 'support@example.com',

    'commands' => [
        'user/create' => CreateUser::class,
        'migrate/create' => Migrate\CreateCommand::class,
        'migrate/generate' => Migrate\GenerateCommand::class,
        'migrate/up' => Migrate\UpCommand::class,
        'migrate/down' => Migrate\DownCommand::class,
        'migrate/list' => Migrate\ListCommand::class,
    ],

    'entityPaths' => [
        '@src/Entity'
    ]
];
