<?php

namespace App\Command;

use App\Entity\BackgroundJob;
use App\Repository\PasswordResetCodeRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PasswordResetCodesDeleteExpiredCommand extends Command
{
    protected static $defaultName = 'app:password-reset-codes:delete-expired';

    private const TIME_IN_HOURS_BEFORE_EXPIRATION = 1;

    private $_em;

    private $_codeRepository;

    public function __construct(
        EntityManagerInterface $em,
        PasswordResetCodeRepository $codeRepository
    ) {
        $this->_em = $em;
        $this->_codeRepository = $codeRepository;

        parent::__construct(self::$defaultName);
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Delete expired password reset codes from the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $backgroundJob = new BackgroundJob();
        $backgroundJob->init(self::$defaultName);

        $backgroundJob->running();
        $this->_em->persist($backgroundJob);
        $this->_em->flush();

        $expired = CarbonImmutable::now()->subHours(self::TIME_IN_HOURS_BEFORE_EXPIRATION);

        $codes = $this->_codeRepository->createQueryBuilder('c')
            ->andWhere('c.createdAt <= :expired')
            ->setParameter('expired', $expired)
            ->getQuery()
            ->getResult();

        $count = sizeof($codes);
        if ($count) {
            foreach ($codes as $code) {
                $this->_em->remove($code);
                $io->text('Deleting:' . $code->getId());
            }
            $this->_em->flush();
    
            $io->success("$count expired password reset code(s) deleted");
        } else {
            $io->success('No expired password reset codes to delete.');
        }

        $backgroundJob->success();
        $this->_em->persist($backgroundJob);
        $this->_em->flush();

        return 0;
    }
}
