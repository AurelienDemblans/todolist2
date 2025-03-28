<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column]
	private ?\DateTimeImmutable $createdAt = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank(message: 'Vous devez saisir un titre.')]
	private ?string $title = null;

	#[ORM\Column(type: Types::TEXT)]
	#[Assert\NotBlank(message: 'Vous devez saisir du contenu.')]
	private ?string $content = null;

	#[ORM\Column(type:'boolean')]
	private ?bool $isDone = null;

	#[ORM\ManyToOne(inversedBy: 'tasks')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $createdBy = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeImmutable $createdAt): static
	{
		if ($this->createdAt !== null) {
			throw new \LogicException("La date de création d'une tâche ne peut pas être modifiée.");
		}
		$this->createdAt = $createdAt;

		return $this;
	}

	public function setId(int $id): static
	{
		$this->id = $id;

		return $this;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): static
	{
		$this->title = $title;

		return $this;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(string $content): static
	{
		$this->content = $content;

		return $this;
	}

	public function isDone(): ?bool
	{
		return $this->isDone;
	}

	public function setIsDone(bool $isDone): static
	{
		$this->isDone = $isDone;

		return $this;
	}

	public function toggle(bool $flag): static
	{
		$this->isDone = $flag;

		return $this;
	}

	public function getCreatedBy(): ?User
	{
		return $this->createdBy;
	}

	public function setCreatedBy(?User $createdBy): static
	{
		if ($this->createdBy !== null) {
			throw new \LogicException("Le créateur d'une tâche ne peut pas être modifié.");
		}
		$this->createdBy = $createdBy;

		return $this;
	}
}
