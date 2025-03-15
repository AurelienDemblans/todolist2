<?php

namespace App\Form;

use App\Entity\User;
use DateTimeImmutable;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
        if ($security->getUser() === null) {
            throw new Exception('Aucun utilisateur connecté');
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['label' => 'Titre : '])
            ->add('content', TextareaType::class, ['label' => 'Contenu : '])
            ->add('is_done', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'data' => false,
                'label' => 'Tâche terminée',
            ])
            ->add('created_at', DateTimeType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
                'disabled' => false,
                'data'   => new DateTimeImmutable(),
                'label' => 'créé le : ',
                'label_attr' => ['style' => 'display: none;'],
                'attr' => ['style' => 'display: none;'],
                'view_timezone' => date_default_timezone_get()
            ])
            ->add('createdBy', EntityType::class, [
                'label' => 'Créé par : ',
                'class' => User::class,
                'choice_label' => 'username',
                'mapped' => true,
                'label_attr' => ['style' => 'display: none;'],
                'attr' => ['style' => 'display: none;'],
                'data' => $this->security->getUser(),
            ])
        ;
    }
}
