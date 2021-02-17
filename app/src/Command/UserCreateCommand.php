<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:users:create';

    private $em;

    private $passwordEncoder;

    private $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a user')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Username'
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'User email'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'User password'
            )
            ->addOption(
                'role',
                null,
                InputOption::VALUE_REQUIRED,
                'User role. Can be USER or ADMIN',
                'USER'
            )
            ->addOption(
                'verified',
                null,
                InputOption::VALUE_NONE,
                'Verify user account'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        // Checking input format
        if ($this->isInvalid($io, $username, $email, $password)) {
            return 1;
        }

        // Checking conflicts
        if ($this->isInConflict($io, $username, $email)) {
            return 0;
        }

        // Creating user
        $role = $input->getOption('role');
        $isVerified = $input->getOption('verified');

        $user = new User();
        $user->setUsername($username)
            ->setPassword(
                $this->passwordEncoder
                    ->encodePassword($user, $password)
            )
            ->setEmail($email)
            ->setRoles(['ROLE_' . $role]);

        if ($isVerified) {
            $user->verify();
        }

        $this->em->persist($user);
        $this->em->flush();

        $io->success("User '$username' created");

        return 0;
    }

    protected function isInvalid(SymfonyStyle $io, String $username, String $email, String $password): boolean
    {
        $invalid = false;

        if (empty($username)) {
            $io->error('Username cannot be empty');
            $invalid = true;
        }
        if (empty($email)) {
            $io->error('Email cannot be empty');
            $invalid = true;
        }
        if (empty($password)) {
            $io->error('Password cannot be empty');
            $invalid = true;
            // TODO Generate random password if empty?
        }

        // TODO Check password security?

        return $invalid;
    }

    protected function isInConflict(SymfonyStyle $io, String $username, String $email): boolean
    {
        $conflict = false;

        if ($this->findByUsername($username)) {
            $io->warning('This username is already taken');
            $conflict = true;
        }
        if ($this->findByEmail($email)) {
            $io->warning('This email address is already taken');
            $conflict = true;
        }

        return $conflict;
    }

    protected function findByUsername(String $username): ?User
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }

    protected function findByEmail(String $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }
}
