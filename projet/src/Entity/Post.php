<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @Vich\Uploadable()
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @var File|null
     * @Assert\Image(
     *     mimeTypes="image/jpeg"
     * )
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $download_link;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=PostDownload::class, mappedBy="post")
     */
    private $downloadCounter;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->downloadCounter = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDownloadLink(): ?string
    {
        return $this->download_link;
    }

    public function setDownloadLink(string $download_link): self
    {
        $this->download_link = $download_link;

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

    /**
     * @return Collection|PostDownload[]
     */
    public function getDownloadCounter(): Collection
    {
        return $this->downloadCounter;
    }

    public function addDownloadCounter(PostDownload $downloadCounter): self
    {
        if (!$this->downloadCounter->contains($downloadCounter)) {
            $this->downloadCounter[] = $downloadCounter;
            $downloadCounter->setPost($this);
        }

        return $this;
    }

    public function removeDownloadCounter(PostDownload $downloadCounter): self
    {
        if ($this->downloadCounter->removeElement($downloadCounter)) {
            // set the owning side to null (unless already changed)
            if ($downloadCounter->getPost() === $this) {
                $downloadCounter->setPost(null);
            }
        }

        return $this;
    }


    /**
     * Permet de savoir sur l'utilisateur a télécharger le jeu (il clique sur le lien)
     *
     * @param User $user
     * @return bool
     */
    public function isDownloadedByUser(User $user){
        foreach ($this->downloadCounter as $download) {
            if ($download->getUser() === $user){
                return true;
            }
        }
        return false;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return Post
     */
    public function setFilename(?string $filename): Post
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Post
     */
    public function setImageFile(?File $imageFile): Post
    {
        $this->imageFile = $imageFile;
        return $this;
    }


}
