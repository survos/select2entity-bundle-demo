<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Intl\Countries;
use App\Entity\Country;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $countries = Countries::getNames();
        foreach ($countries as $alpha2=>$name) {
            $country = new Country();
            $country
                ->setName($name)
                ->setAlpha2($alpha2);
            $manager->persist($country);
        }
        $manager->flush();
    }
}
