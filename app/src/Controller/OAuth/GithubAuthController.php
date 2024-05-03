<?php

namespace App\Controller\OAuth;

use App\Builder\UserBuilder;
use App\DataTransferObject\AccessTokenDto;
use App\DataTransferObject\EmailDto;
use App\DataTransferObject\GithubUserDto;
use App\Entity\GithubAccessToken;
use App\Entity\OAuthHash;
use App\Entity\User;
use App\Repository\GithubAccessTokenRepository;
use App\Repository\UserRepository;
use App\Utility\RandomGenerator;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GithubClient;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/oauth/github', name: "oauth_github")]
class GithubAuthController extends AbstractController
{
    protected const SESSION_OAUTH_HASH = 'oauth_github_hash';

    public function __construct(
        protected ClientRegistry $clientRegistry,
        protected GithubAccessTokenRepository $repo,
        protected HttpClientInterface $httpClient,
        protected EntityManagerInterface $entityManager,
        protected UrlGeneratorInterface $urlGenerator,
        protected SerializerInterface $serializer,
        protected UserBuilder $userBuilder,
        protected UserRepository $userRepository,
        protected RandomGenerator $randomGenerator
    ) {
        parent::__construct($entityManager, $urlGenerator, $serializer);
    }

    /**
     * Link to this controller to start the "connect" process
     */
    #[Route("/connect/hash-{hash}", name: "_connect", methods: ["GET"])]
    public function connectAction(
        string $hash,
        Request $request
    ): RedirectResponse {
        $hash = $this->getOAuthHash($hash);

        $session = $request->getSession();
        $session->set(static::SESSION_OAUTH_HASH, $hash->getHash());

        // will redirect to Github!
        return $this->clientRegistry
            ->getClient('github') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'read:user',
                'user:email',
            ]);
    }

    /**
     * After going to Github, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     * ** if you want to *authenticate* the user, then leave this method blank and create a Guard authenticator
     */
    #[NoReturn] #[Route("/connect/check", name: "_connect_check", methods: ["GET"])]
    public function  connectCheckAction(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $oauthHash =  $session->get(static::SESSION_OAUTH_HASH);
        if (null === $oauthHash) {
            throw $this->createNotFoundException();
        }
        $hash = $this->getOAuthHash($oauthHash);

        $accessTokenDto = $this->getGithubAccessToken();

        $githubUserDto = $this->getGithubUser($accessTokenDto);

        $this->validateIfGithubEmailsAreInSystem($githubUserDto, $hash);
        $this->storeAccessTokens($githubUserDto, $accessTokenDto, $hash);

        return $this->json([
            'message' => 'success',
        ], Response::HTTP_OK);
    }

    protected function getOAuthHash(string $hash): OAuthHash
    {
        $hash = $this->entityManager->getRepository(OAuthHash::class)->findOneBy(['hash' => $hash]);
        if (null === $hash) {
            throw $this->createAccessDeniedException();
        }

        $diff = (new DateTime())->getTimestamp() - $hash->getExpireAt()->getTimestamp();
        if (300 < $diff) {
            throw $this->createAccessDeniedException();
        }

        return $hash;
    }

    protected function getGithubAccessToken(): AccessTokenDto
    {
        /** @var GithubClient $client */
        $client = $this->clientRegistry->getClient('github');

        $githubAccessToken = $client->getAccessToken([
            'read:user',
            'user:email',
        ]);

        if ($githubAccessToken->getExpires() instanceof DateTimeInterface) {
            $diff = (new DateTime())->getTimestamp() - $githubAccessToken->getExpires();
            if (300 < $diff) {
                throw $this->createAccessDeniedException();
            }
        }

        return $this->serializer->denormalize($githubAccessToken->jsonSerialize(), AccessTokenDto::class);
    }

    protected function getGithubUser(AccessTokenDto $token): GithubUserDto
    {
        $detailsResponse = $this->httpClient->request(
            'GET',
            'https://api.github.com/user',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token->accessToken,
                ],
            ]
        );

        $emailResponse = $this->httpClient->request(
            'GET',
            'https://api.github.com/user/emails',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token->accessToken,
                ],
            ]
        );

        $data = $detailsResponse->toArray();

        $emailCollection = new ArrayCollection();
        foreach ($emailResponse->toArray() as $email)
        {
            if(!str_contains($email['email'], 'users.noreply.github.com')) {
                $emailCollection->add($this->serializer->denormalize($email, EmailDto::class));
            }
        }
        $data['emails'] = $emailCollection->toArray();

        return $this->serializer->denormalize($data, GithubUserDto::class);
    }

    protected function validateIfGithubEmailsAreInSystem(GithubUserDto $githubUserDto, OAuthHash $hash): void
    {
        foreach ($githubUserDto->emails as $emailDto)
        {
            if ($emailDto->email === $hash->getOwner()->getEmail()) {
                $githubUserDto->email = $emailDto->email;

                break;
            }
        }

        if ($githubUserDto->email !== $hash->getOwner()->getEmail()) {
            throw $this->createAccessDeniedException();
        }
    }

    protected function storeAccessTokens(GithubUserDto $githubUserDto, AccessTokenDto $accessTokenDto, OAuthHash $OAuthHash): void
    {
        $accessTokenRepo = $this->entityManager->getRepository(GithubAccessToken::class);
        $oAuthHashRepo = $this->entityManager->getRepository(OAuthHash::class);
        foreach ($githubUserDto->emails as $emailDto)
        {
            $token = new GithubAccessToken();
            $token->setAccessToken($accessTokenDto->accessToken);

            if ($accessTokenDto->expires instanceof DateTimeInterface) {
                $token->setExpireAt($accessTokenDto->expires);
            }

            $token->setOwner($OAuthHash->getOwner());
            $token->setEmail($emailDto->email);
            $token->setFirstname($githubUserDto->name);
            $token->setUsername($githubUserDto->login);
            $token->setUserId((string) $githubUserDto->id);

            $metadata = [
                'user' => $this->serializer->normalize($githubUserDto),
                'token' => $this->serializer->normalize($accessTokenDto),
                'hash' => $this->serializer->normalize(
                    $OAuthHash,
                    'array',
                    [
                        AbstractNormalizer::CALLBACKS => [
                            'owner' => function (User $value) { return $value->getRawId(); },
                        ],
                        AbstractNormalizer::IGNORED_ATTRIBUTES => ['rawId','object'],
                    ]
                ),
            ];

            $token->setMetadata($metadata);

            $existed = $accessTokenRepo->findOneBy([
                'owner' => $token->getOwner(),
                'email' => $token->getEmail(),
                'username' => $token->getUsername(),
            ]);

            if (null !== $existed) {
                $accessTokenRepo->remove($existed);
                $accessTokenRepo->save();
            }

            $accessTokenRepo->add($token);
        }

        $oAuthHashRepo->remove($OAuthHash);

        $this->entityManager->flush();
    }
}
