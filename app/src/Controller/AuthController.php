<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\UserBuilder;
use App\Repository\UserRepository;
use App\DataTransferObject\UserRegistrationDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth', name: "api_auth")]
class AuthController extends AbstractController
{
    #[Route('/register', name: '_register', methods: ["POST"])]
    public function index(
        #[MapRequestPayload] UserRegistrationDto $userRegisterDto,
        UserBuilder $userBuilder,
        UserRepository $repo
    ): JsonResponse {

        $user = $userBuilder->base($userRegisterDto->email, $userRegisterDto->password);

        $repo->add($user);
        $repo->save();

        return $this->json([
            'message' => 'success',
        ], Response::HTTP_OK);
    }

    #[Route('/logout', name: '_logout', defaults: ['deviceType' => 'web'], methods: ['GET', 'POST', 'OPTIONS'])]
    public function logout(): never
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
