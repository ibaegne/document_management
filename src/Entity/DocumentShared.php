<?php

namespace App\Entity;

use App\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\DocumentSharedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentSharedRepository::class)
 */
class DocumentShared
{
    use TimestampableTrait;

    public const ACCESS_READ = 1;
    public const ACESS_EDITOR = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Document::class, inversedBy="documentsShared")
     * @ORM\JoinColumn(nullable=false)
     */
    private Document $document;

    /**
     * @ORM\Column(type="integer")
     */
    private int $access;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="documentsShared")
     */
    private Collection $receivers;

    public function __construct()
    {
        $this->receivers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getAccess(): ?int
    {
        return $this->access;
    }

    public function setAccess(int $access): self
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getReceivers(): Collection
    {
        return $this->receivers;
    }

    public function addReceiver(User $receiver): self
    {
        if (!$this->receivers->contains($receiver)) {
            $this->receivers[] = $receiver;
        }

        return $this;
    }

    public function removeReceiver(User $receiver): self
    {
        $this->receivers->removeElement($receiver);

        return $this;
    }
}
