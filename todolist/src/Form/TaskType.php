<?php

namespace App\Form;

use App\Entity\Task;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$task          = $options['data'] ?? null;
		$taskHasId     = $task instanceof Task && $task->getId() !== null;
		$defaultIsDone = $taskHasId ? $task->isDone() : false;

		$builder
			->add('title', null, ['label' => 'Titre : '])
			->add('content', TextareaType::class, ['label' => 'Contenu : '])
			->add('is_done', ChoiceType::class, [
				'choices'  => [
					'Oui' => true,
					'Non' => false,
				],
				'expanded' => true,
				'data'     => $defaultIsDone,
				'label'    => 'Tâche terminée',
			]);
		if ($options['from'] === 'ADD') {
			$builder->add('created_at', DateTimeType::class, [
				'widget'        => 'choice',
				'input'         => 'datetime_immutable',
				'disabled'      => false,
				'data'          => new DateTimeImmutable(),
				'label'         => 'créé le : ',
				'label_attr'    => ['style' => 'display: none;'],
				'attr'          => ['style' => 'display: none;'],
				'view_timezone' => date_default_timezone_get(),
			]);
		}
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Task::class,
			'from'       => 'ADD',
		]);

		$resolver->setAllowedValues('from', ['ADD', 'EDIT']);
	}
}
