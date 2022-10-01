
symfony console survos:make:command app:import-countries "Import the countries from symfony/intl to the database"

cat << 'EOF' | symfony console survos:class:update  App\\Command\\AppImportCountriesCommand --diff \
  -m __invoke \
  --use App\\Entity\\Country \
  --use "Symfony\Component\Intl\Countries" \
  --inject "App\Repository\CountryRepository" \
  --inject 'Doctrine\ORM\EntityManagerInterface $em' \
  && vendor/bin/ecs check src/Command --fix
        $em->createQuery('DELETE FROM ' . Country::class)->execute();

        // the invoke body goes here, NOT the entire signature
        $countries = Countries::getNames();
        foreach ($countries as $alpha2 => $name) {
            $country = new Country();
            $country
                ->setName($name)
                ->setAlpha2($alpha2);
            $em->persist($country);
        }
        $em->flush();
        $io->success(sprintf("%d countries imported.", $countryRepository->count([])));
EOF
exit 1;


cat <<'EOF' | symfony console survos:class:update  App\\Repository\\TestRepository  --diff  \
  --implements "Survos\CoreBundle\Traits\QueryBuilderHelperInterface" \
  --trait "Survos\CoreBundle\Traits\QueryBuilderHelperTrait" \
  && vendor/bin/ecs check src/Repository --fix
EOF

exit 1;

bin/console survos:make:command app:load-congress -barg purge
cat < EOL | bin/console survos:make:method "App\Command\AppLoadCongressCommand" -m __invoke \
  --use "Doctrine\ORM\EntityManagerInterface" \
  --inject EntityManagerInterface$em
  --inject Doctrine\ORM\EntityManagerInterface$em #automatically add use?
  --trait

EOL

exit 1;


cat <<'EOF' | symfony console make:method  App\\Service\\XService
public function test(): void {}
EOF


cat <<'EOF' | symfony console make:method  App\\Controller\\AppController
public function index(): Response {
  return $this->render('x.html.twig', [
     'list' => $repo->findAll()
     ]
     );
}
EOF

#cat <<'EOF' | symfony console make:method  "App\\Service\\AppService" calcScore
#// this is just the body
#return $a + b;

exit 1;


git clean -f src/Service/XService.php -f
cat <<'EOF' | symfony console survos:make:service  X
// src/Service/XService.php
public function test(): void {}
EOF
cat src/Service/XService.php



