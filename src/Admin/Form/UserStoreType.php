<?php

namespace App\Admin\Form;

use App\Entity\User;
use App\Entity\UserStore;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserStoreType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name', // Melhor visualização, mostra o nome do usuário
                'label' => 'Usuário a vincular',
                'placeholder' => 'Selecione um usuário',
                'required' => true,
            ])
            ->add('storeRole', ChoiceType::class, [
                'label' => 'Papel na Loja',
                'choices' => [
                    'Gerente' => 'manager',
                    'Funcionário' => 'employee',
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ])
            ->add('permissions', ChoiceType::class, [
                'label' => 'Permissões Específicas',
                'choices' => [
                    'Visualizar Agendamentos' => 'view_appointments',
                    'Gerenciar Agendamentos' => 'manage_appointments',
                    'Gerenciar Clientes' => 'manage_clients',
                    'Gerenciar Produtos' => 'manage_products',
                ],
                'expanded' => true, // Para exibir como checkboxes
                'multiple' => true, // Para permitir selecionar várias opções
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => UserStore::class,
        ]);
    }
}
