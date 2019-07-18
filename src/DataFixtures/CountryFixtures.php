<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Intl\Countries;

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
