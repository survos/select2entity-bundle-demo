<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\AddNewCountryFormType;
use App\Form\SingleSelectFormType;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route(path: '/index', name: 'app_index')]
    public function index()
    {
        $forms = [
            SingleSelectFormType::class,
            AddNewCountryFormType::class
        ];

        return $this->render('app/index.html.twig', [
            'forms' => $forms
        ]);
    }

    #[Route(path: '/', name: 'home')]
    #[Route(path: '/form/', name: 'app_show_form')]
    public function showForm(Request $request)
    {
        $formClass = $request->get('formClass',SingleSelectFormType::class);
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
            'errorMessage' => $errorMessage
        ]);
    }

    #[Route(path: '/add_country_form/', name: 'app_add_country_form')]
    public function addCountryForm(Request $request)
    {
        $formClass = $request->get('formClass',\App\Form\SingleSelectFormType::class);
        $defaults = [];
        $form = $this->createForm($formClass, $defaults);

        return $this->render('app/showForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/country_autocomplete.json', name: 'app_country_autocomplete')]
    public function CountryAutocomplete(Request $request, CountryRepository $repository)
    {
        $q = $request->get('q');
        $matches = $repository->createQueryBuilder('c')
            ->where("c.name LIKE :searchString")
            ->setParameter('searchString', $q . '%')
            ->getQuery()
            ->getResult();

        $data = array_map(fn(Country $country) => ['id' => $country->getId(), 'text' => $country->getName()], $matches);
        $data = array_values($data);

        $data = ['results' => $data];
        return new JsonResponse($data);
    }

}
