<?php

namespace App\DataFixtures;

use App\Entity\BackgroundJob;
use App\Entity\Media;
use App\Entity\Parameter;
use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $backgroundJobS = new BackgroundJob();
        $backgroundJobS
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now());
        $backgroundJobS->init('Fixture success job');
        $backgroundJobS->success();
        $manager->persist($backgroundJobS);

        $backgroundJobE = new BackgroundJob();
        $backgroundJobE
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now());
        $backgroundJobE->init('Fixture error job');
        $backgroundJobE->error();
        $manager->persist($backgroundJobE);

        $parameterAppUrl = new Parameter();
        $parameterAppUrl
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now())
            ->setName('APP_PUBLIC_URL')
            ->setType(Parameter::STRING_TYPE)
            ->setValue('http://localhost:8000')
        ;
        $manager->persist($parameterAppUrl);

        $media = new Media();
        $media
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now())
            ->setName('DummyMedia.png')
            ->setFilename('DummyMedia123456789.png')
            ->setType('image/png')
        ;
        $manager->persist($media);

        $manager->flush();
    }
}
