<?php

namespace App\Admin\Form;

use App\Entity\Store;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('cnpj', TextType::class, [
                'label' => 'CNPJ',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('corporate_name', TextType::class, [
                'label' => 'Razão Social',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('dt_foundation', DateType::class, [
                'label' => 'Data de Fundação',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Store::class,
        ]);
    }
}
