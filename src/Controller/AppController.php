<?php

namespace App\Controller;

use App\Command\AppImportCountriesCommand;
use App\Entity\Country;
use App\Entity\Test;
use App\Form\AddNewCountryFormType;
use App\Form\SingleSelectFormType;
use App\Repository\CountryRepository;
use App\Service\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use SebastianBergmann\Diff\Differ;
use Survos\Bundle\MakerBundle\Service\MakerService;
use Survos\CoreBundle\Entity\RouteParametersInterface;
use Survos\CoreBundle\Entity\RouteParametersTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Framework\Workflows\WorkflowsConfig;

class AppController extends AbstractController
{
    #[Route(path: '/make-method', name: 'app_make_method')]
    public function makeMethod(MakerService $makerService)
    {
        $testClass = AppImportCountriesCommand::class;
        $traits = [
//            RouteParametersTrait::class,
        ];
        $uses = [
//            RouteParametersTrait::class,
        ];

        $implements = [
//            RouteParametersInterface::class,
        ];

        $injects = [
            EntityManagerInterface::class . ' $em',
            CountryRepository::class . '$repo',
        ];


        $methodName = 'getPopulation';
        $methodName = '__invoke';
        $php = <<< 'EOF'
    $countries = Countries::getNames();
    foreach ($countries as $alpha2=>$name) {
        $country = new Country();
        $country
            ->setName($name)
            ->setAlpha2($alpha2);
        $this-em->persist($country);
    }
    $this->em->flush();
EOF;

        $refectionClass = $makerService->getReflectionClass($testClass);

        $source = $makerService->modifyClass($refectionClass, traits: $traits, uses: $uses, implements: $implements, methodName: $methodName, injects: $injects, php: $php);



        return new Response((new Differ)->diff($refectionClass->getLocatedSource()->getSource(), $source), 200, ['Content-Type' => 'text/php']);

    }

        #[Route(path: '/index', name: 'app_index')]
    public function index()
    {
        $forms = [
            SingleSelectFormType::class,
            AddNewCountryFormType::class,
        ];

        return $this->render('app/index.html.twig', [
            'forms' => $forms,
        ]);
    }

    #[Route(path: '/', name: 'home')]
    #[Route(path: '/form/', name: 'app_show_form')]
    public function showForm(Request $request)
    {
        $formClass = $request->get('formClass', SingleSelectFormType::class);
        $defaults = [];
        $form = $this->createForm($formClass, $defaults);
        $errorMessage = '';
        $results = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = $form->all();
            // dump($request, $form->all());
        }
        try {
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $reflector = new \ReflectionClass($formClass);

        return $this->render('app/showForm.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
            'formClass' => $formClass,
            'source' => file_get_contents($reflector->getFileName()),
            'errorMessage' => $errorMessage,
        ]);
    }

    #[Route(path: '/add_country_form/', name: 'app_add_country_form')]
    public function addCountryForm(Request $request)
    {
        $formClass = $request->get('formClass', \App\Form\SingleSelectFormType::class);
        $defaults = [];
        $form = $this->createForm($formClass, $defaults);

        return $this->render('app/showForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/country_autocomplete.json', name: 'app_country_autocomplete')]
    public function CountryAutocomplete(Request $request, EntityManagerInterface $entityManager, CountryRepository $repository)
    {
        $q = $request->get('q');
        $matches = $repository->createQueryBuilder('c')
            ->where('c.name LIKE :searchString')
            ->setParameter('searchString', $q . '%')
            ->getQuery()
            ->getResult();

        $data = array_map(fn (Country $country) => [
            'id' => $country->getId(),
            'text' => $country->getName(),
        ], $matches);
        $data = array_values($data);

        $data = [
            'results' => $data,
        ];
        return new JsonResponse($data);
    }
}
