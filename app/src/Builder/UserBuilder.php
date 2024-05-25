<?php

declare(strict_types=1);

namespace App\Builder;

use App\DataTransferObject\GithubUserDto;
use App\Entity\User;
use App\Exceptions\AlreadyExists;
use App\Repository\UserRepository;
use App\Services\DutyTeamSlackBot\DataTransferObject\reader\UserInfoDto;
use App\Utility\EmailHasher;
use App\Utility\RandomGenerator;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function strlen;

class UserBuilder implements IEntityBuilder
{
    public function __construct(
        protected readonly UserPasswordHasherInterface $passwordHasher,
        protected readonly RandomGenerator $randomGenerator,
        protected readonly UserRepository $userRepository,
        protected readonly EmailHasher $emailHasher
    ) {}

    public function base(
        string $email,
        string $password,
        ?string $username = null
    ): User{
        if (strlen($email) < 6) {
            throw new InvalidArgumentException('Invalid email length. Expect string length greater than 5.');
        }

        $exist = $this->userRepository->findByEmail($email);
        if ($exist instanceof User) {
            throw new AlreadyExists('Such a user already exists.');
        }

        $user = new User();

        $user->setEmail($email);
        $user->setPassword($password);

        if (null === $username) {
            $rndGen = new RandomGenerator();
            $user->setUsername($rndGen->uniqueId('u'));
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        $user->setPassword($hashedPassword);

        return $user;
    }

    public function github(GithubUserDto $githubUserDto): User
    {
        $email = null;
        foreach ($githubUserDto->emails as $emailDto)
        {
            if ($emailDto->primary) {
                $email = $emailDto->email;

                break;
            }
        }

        $password = $this->randomGenerator->sha256($email);
        $username = $githubUserDto->username ?? null;

        return $this->base($email, $password, $username);
    }

    public function slack(UserInfoDto $dto): User
    {
        $password = $this->randomGenerator->sha256($dto->email);
        $username = $dto->username ?? null;

        $user = $this->base($dto->email, $password, $username);
        $user->setFirstName($dto->firstName);
        $user->setLastname($dto->lastName);
        $user->setIsDeleted($dto->isDeleted);
        $user->setIsEmailVerified($dto->isEmailConfirmed);

        return $user;
    }
}
