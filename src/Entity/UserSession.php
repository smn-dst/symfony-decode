<?php

namespace App\Entity;

use App\Repository\UserSessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSessionRepository::class)]
#[ORM\Table(name: 'user_session')]
class UserSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sessionStart = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sessionEnd = null;

    #[ORM\Column]
    private ?int $durationSeconds = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'userSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionStart(): ?\DateTimeImmutable
    {
        return $this->sessionStart;
    }

    public function setSessionStart(\DateTimeImmutable $sessionStart): static
    {
        $this->sessionStart = $sessionStart;

        return $this;
    }

    public function getSessionEnd(): ?\DateTimeImmutable
    {
        return $this->sessionEnd;
    }

    public function setSessionEnd(\DateTimeImmutable $sessionEnd): static
    {
        $this->sessionEnd = $sessionEnd;

        return $this;
    }

    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds): static
    {
        $this->durationSeconds = $durationSeconds;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
