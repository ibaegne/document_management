<?php

namespace App\Entity;

use App\Model\TimestampableTrait;
use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\Table(indexes={@ORM\Index(name="safe_name_idx", columns={"safe_name"})})
 * @UniqueEntity(
 *     fields={"name", "owner", "extension"},
 *     errorPath="name",
 *     message="Vous avez déjà ajouté un document avec ce nom."
 * )
 */
class Document
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $owner;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=100, unique="true")
     */
    private string $safeName;

    private UploadedFile $file;

    /**
     * @ORM\OneToMany(targetEntity=DocumentShared::class, mappedBy="document", cascade={"persist", "remove"})
     */
    private Collection $documentsShared;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 5,
     *      minMessage = "Your extension must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=5)
     */
    private string $extension;

    public function __construct()
    {
        $this->documentsShared = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSafeName(): ?string
    {
        return $this->safeName;
    }

    public function setSafeName(string $safeName): self
    {
        $this->safeName = $safeName;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file):  self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection<int, DocumentShared>
     */
    public function getDocumentsShared(): Collection
    {
        return $this->documentsShared;
    }

    public function addDocumentShared(DocumentShared $documentShared): self
    {
        if (!$this->documentsShared->contains($documentShared)) {
            $this->documentsShared[] = $documentShared;
            $documentShared->setDocument($this);
        }

        return $this;
    }

    public function removeDocumentShared(DocumentShared $documentShared): self
    {
        if ($this->documentsShared->removeElement($documentShared)) {
            // set the owning side to null (unless already changed)
            if ($documentShared->getDocument() === $this) {
                $documentShared->setDocument(null);
            }
        }

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }
}
