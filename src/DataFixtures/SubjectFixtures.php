<?php

namespace App\DataFixtures;

use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SubjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $owner = $manager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'john.doe@gmail.com']);

        $subject = new Subject();
        $subject
            ->setName('company_trainer_1')
            ->setOwner($owner);

        $manager->persist($subject);
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }
}
