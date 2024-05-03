<?php

namespace App\Controller\OAuth;

use App\Controller\AbstractController;
use App\Entity\OAuthHash;
use App\Repository\OAuthHashRepository;
use App\Utility\RandomGenerator;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/generator/hash', name: "generator_hash")]
class HashGeneratorController extends AbstractController
{
    #[Route("/oauth", name: "_oauth", methods: ["GET"])]
    public function oauth(
        RandomGenerator $randomGenerator,
        OAuthHashRepository $repository
    ): JsonResponse {
        $user = $this->getUser();
        $hash = $repository->findOneBy(['owner' => $user]);

        if (null !== $hash) {
            $repository->remove($hash);
            $repository->save();
        }

        $hash = $randomGenerator->sha256($user->getRawId());
        $oAuthHash = new OAuthHash();
        $oAuthHash->setHash($hash);
        $oAuthHash->setOwner($user);
        $oAuthHash->setExpireAt(new DateTime('+5 min'));

        $repository->add($oAuthHash);
        $repository->save();

        return new JsonResponse(['hash' => $hash]);
    }
}
