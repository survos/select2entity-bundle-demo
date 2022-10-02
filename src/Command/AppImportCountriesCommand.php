<?php

namespace App\Command;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Intl\Countries;
use Zenstruck\Console\Attribute\Option;
use Zenstruck\Console\ConfigureWithAttributes;

use Zenstruck\Console\InvokableServiceCommand;
use Zenstruck\Console\IO;

use Zenstruck\Console\RunsCommands;
use Zenstruck\Console\RunsProcesses;

#[AsCommand('app:import-countries', 'Import the countries from symfony/intl to the database')]
final class AppImportCountriesCommand extends InvokableServiceCommand
{
    use ConfigureWithAttributes;
    use RunsCommands;
    use RunsProcesses;

    public function __invoke(
        CountryRepository $countryRepository,
        EntityManagerInterface $em,
        IO $io,

        // custom injections
        // UserRepository $repo,

        // expand the arguments and options

        #[Option(name: 'role', shortcut: 'r')]
        array $roles,
    ): void {
        $em->createQuery('DELETE FROM ' . Country::class)->execute();

//        array_walk($x=Countries::getNames(), fn($name, $alpha2) => (new Country())->setName($name)->setAlpha2($alpha2));
        $countries = Countries::getNames();
        foreach ($countries as $alpha2 => $name) {
            $country = (new Country())
                ->setName($name)
                ->setAlpha2($alpha2);
            $countryRepository->save($country);
        }
        $em->flush();
        $io->success(sprintf('%d countries imported.', $countryRepository->count([])));
    }
}
