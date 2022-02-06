<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups("list")]
    private int $id;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Event::class, cascade:["persist"], orphanRemoval: true)]
    private Collection $events;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups("list")]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subjects')]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[Pure]
    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setSubject($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getSubject() === $this) {
                $event->setSubject(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }
}
