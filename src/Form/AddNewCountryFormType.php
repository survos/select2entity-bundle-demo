<?php

namespace App\Form;

use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class AddNewCountryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country_with_add_option', Select2EntityType::class, [
                'multiple' => true,
                'remote_route' => 'app_country_autocomplete',
                'class' => Country::class,
                'primary_key' => 'id',
                'text_property' => 'name',
                'cache' => 0,
                'page_limit' => 10,
                'required' => false,
                'allow_clear' => true,
                'language' => 'en',
                'placeholder' => 'Select or Add Countries',
                'attr' => [
                    'class' => 'js-select2'
                ],
                'allow_add' => [
                    'enabled' => true
                ]
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
