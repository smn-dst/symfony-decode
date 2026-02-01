<?php

namespace App\Entity;

use App\Repository\UserGamificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGamificationRepository::class)]
#[ORM\Table(name: 'user_gamification')]
#[ORM\HasLifecycleCallbacks]
class UserGamification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $level = null;

    #[ORM\Column]
    private int $totalXp = 0;

    #[ORM\Column]
    private int $currentStreakDays = 0;

    #[ORM\Column]
    private int $longestStreakDays = 0;

    #[ORM\Column]
    private int $citiesExploredCount = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(inversedBy: 'userGamification', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt ??= new \DateTimeImmutable();
        $this->updatedAt ??= new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getTotalXp(): int
    {
        return $this->totalXp;
    }

    public function setTotalXp(int $totalXp): static
    {
        $this->totalXp = $totalXp;

        return $this;
    }

    public function getCurrentStreakDays(): int
    {
        return $this->currentStreakDays;
    }

    public function setCurrentStreakDays(int $currentStreakDays): static
    {
        $this->currentStreakDays = $currentStreakDays;

        return $this;
    }

    public function getLongestStreakDays(): int
    {
        return $this->longestStreakDays;
    }

    public function setLongestStreakDays(int $longestStreakDays): static
    {
        $this->longestStreakDays = $longestStreakDays;

        return $this;
    }

    public function getCitiesExploredCount(): int
    {
        return $this->citiesExploredCount;
    }

    public function setCitiesExploredCount(int $citiesExploredCount): static
    {
        $this->citiesExploredCount = $citiesExploredCount;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
