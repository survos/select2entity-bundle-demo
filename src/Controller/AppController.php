<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\SingleSelectFormType;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app")
     */
    public function index()
    {
        $forms = [
            SingleSelectFormType::class
        ];
        return $this->render('app/index.html.twig', [
            'forms' => $forms
        ]);
    }

    /**
     * @Route("/form/{formClass}", name="app_show_form")
     */
    public function showForm(Request $request, $formClass)
    {
        $defaults = [];
        $form = $this->createForm($formClass, $defaults);

        return $this->render('app/showForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/country_autocomplete.json", name="app_country_autocomplete")
     */
    public function CountryAutocomplete(Request $request, CountryRepository $repository)
    {
        $q = $request->get('q');
        $matches = $repository->createQueryBuilder('c')
            ->where("c.name LIKE :searchString")
            ->setParameter('searchString', $q . '%')
            ->getQuery()
            ->getResult();

        $data = array_map(function(Country $country) use ($request) {
            return ['id' => $country->getId(), 'text' => $country->getName()];
        }, $matches);
        $data = array_values($data);

        $data = ['results' => $data];
        return new JsonResponse($data);
    }

}
