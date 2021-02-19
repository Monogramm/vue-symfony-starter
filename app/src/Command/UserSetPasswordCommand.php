<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSetPasswordCommand extends Command
{
    protected static $defaultName = 'app:users:set-password';

    /**
     * @var EntityManagerInterface
     */
    private $_em;

    /**
     * @var UserRepository
     */
    private $_userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $_passwordEncoder;

    /**
     * @var PasswordGenerator
     */
    private $_passwordGenerator;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        PasswordGenerator $passwordGenerator
    ) {
        $this->_em = $em;
        $this->_userRepository = $userRepository;
        $this->_passwordEncoder = $passwordEncoder;
        $this->_passwordGenerator = $passwordGenerator;

        parent::__construct(self::$defaultName);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Set user password')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Username'
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                'User password (randomly generated if not defined)'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getOption('password');

        // Checking input format
        $invalid = false;
        if (empty($username)) {
            $io->error('Username cannot be empty');
            $invalid = true;
        }
        if (empty($password)) {
            $password = $this->_passwordGenerator->generate(12);
            $io->warning("No password provided. Randomly generating a new password: $password");
        }
        // TODO Check password security?

        if ($invalid) {
            return 1;
        }

        // Setting user password
        $user = $this->findByUsername($username);
        if (empty($user)) {
            $io->error('No user found with this username');
            return 1;
        }

        $user->setPassword(
            $this->_passwordEncoder
                    ->encodePassword($user, $password)
        );

        $this->_em->persist($user);
        $this->_em->flush();

        $io->success("User '$username' password reset");

        return 0;
    }

    protected function findByUsername(String $username): ?User
    {
        return $this->_userRepository->findOneBy(['username' => $username]);
    }
}
