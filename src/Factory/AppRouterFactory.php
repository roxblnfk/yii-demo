<?php

namespace App\Factory;

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Controller\AuthController;
use App\Controller\ContactController;
use App\Controller\SiteController;
use App\Controller\UserController;
use App\Middleware\ActionCaller as Action;
use App\Middleware\SetFormat;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Injector\Injector;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectorInterface;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $mw = static function (
            string $class,
            array $constructor = [],
            string $method = 'process',
            array $params = []
        ) use ($container) {
            return static function (
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ) use ($container, $class, $constructor, $method, $params): ResponseInterface {
                $params += ['request' => $request, 'handler' => $handler];
                return (new Injector($container))->invoke([new $class(...$constructor), $method], $params);
            };
        };

        $routes = [
            // Lonely pages of site
            Route::get('/', [SiteController::class, 'index'])
                ->name('site/index'),
            Route::methods([Method::GET, Method::POST], '/contact', [ContactController::class, 'contact'])
                ->name('site/contact'),
            Route::methods([Method::GET, Method::POST], '/login', [AuthController::class, 'login'])
                ->name('site/login'),
            Route::get('/logout', [AuthController::class, 'logout'])
                ->name('site/logout'),

            // User
            Group::create('/user', [
                // Index
                Route::get('[/page-{page:\d+}]', [UserController::class, 'index'])
                    ->name('user/index'),
                // Profile page
                Route::get('/{login}', [UserController::class, 'profile'])
                    ->name('user/profile'),
            ]),

            // Blog routes
            Group::create('/blog', [
                // Index
                Route::get('[/page{page:\d+}]', [BlogController::class, 'index'])
                    ->name('blog/index'),
                // Post page
                Route::get('/page/{slug}', [PostController::class, 'index'])
                    ->name('blog/post'),
                // Tag page
                Route::get('/tag/{label}[/page{page:\d+}]', [TagController::class, 'index'])
                    ->name('blog/tag'),
                // Archive
                Group::create('/archive', [
                    // Index page
                    Group::create('', [
                        Route::get('', $mw(Action::class, [ArchiveController::class, 'index', $container]))
                            ->addMiddleware($mw(SetFormat::class, ['html', null]))
                            ->name('blog/archive/index'),
                        Route::get('/print_r', $mw(Action::class, [ArchiveController::class, 'index', $container]))
                            ->addMiddleware($mw(SetFormat::class, ['print_r']))
                            ->name('blog/archive/index/print_r'),
                        Route::get('/xml', $mw(Action::class, [ArchiveController::class, 'index', $container]))
                            ->addMiddleware($mw(SetFormat::class, ['xml']))
                            ->name('blog/archive/index/xml'),
                        Route::get('/json', $mw(Action::class, [ArchiveController::class, 'index', $container]))
                            ->name('blog/archive/index/json'),
                        Route::get('/custom', $mw(Action::class, [ArchiveController::class, 'index', $container]))
                            ->name('blog/archive/index/custom'),
                    ]),
                    // Yearly page
                    Route::get('/{year:\d+}', [ArchiveController::class, 'yearlyArchive'])
                        ->name('blog/archive/year'),
                    // Monthly page
                    Route::get('/{year:\d+}-{month:\d+}[/page{page:\d+}]', [ArchiveController::class, 'monthlyArchive'])
                        ->name('blog/archive/month')
                ])
                    ->addMiddleware($mw(SetFormat::class, ['json']))
            ]),
        ];

        $collector =  $container->get(RouteCollectorInterface::class);
        $collector->addGroup(Group::create(null, $routes));

        return new UrlMatcher(new RouteCollection($collector));
    }
}
