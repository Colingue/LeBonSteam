<?php

namespace App\Entity;

use App\Repository\PostDownloadRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostDownloadRepository::class)
 */
class PostDownload
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="downloadCounter")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="postDownloads")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

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
}
