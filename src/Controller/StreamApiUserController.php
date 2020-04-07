<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Stream\Value\DataResponseProvider;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Reader\Sort;

class StreamApiUserController
{
    public function index(ORMInterface $orm): array
    {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);

        $dataReader = $userRepo->findAll()->withSort((new Sort([]))->withOrderString('login'));
        /** @var $users User[] */
        $users = $dataReader->read();

        $items = [];
        foreach ($users as $user) {
            $items[] = ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')];
        }

        return $items;
    }

    public function profile(Request $request, ORMInterface $orm): DataResponseProvider
    {
        /** @var UserRepository $userRepository */
        $userRepository = $orm->getRepository(User::class);
        $login = $request->getAttribute('login', null);

        /** @var User $user */
        $user = $userRepository->findByLogin($login);

        if ($user === null) {
            return (new DataResponseProvider(null))->setCode(404);
        }

        return new DataResponseProvider(
            ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')]
        );
    }
}
