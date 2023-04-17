<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'todolist_task')]
#[ORM\Entity(repositoryClass: \App\Repository\TaskRepository::class)]
class Task
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[Assert\NotBlank(message: 'app.task.title.not.blank')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'app.task.title.min.length',
        maxMessage: 'app.task.title.max.length',
    )]
    #[ORM\Column(type: 'string')]
    private ?string $title = null;

    #[Assert\NotBlank(message: 'app.task.content.not.blank')]
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isDone = false;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, inversedBy: 'tasks')]
    private ?User $user = null;

    #[Assert\NotBlank(message: 'app.task.category.not.blank')]
    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Category $category = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function toggle(bool $flag): self
    {
        $this->isDone = $flag;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
