<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Campaign::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $campaign;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity=CommentRating::class, mappedBy="comment", orphanRemoval=true)
     */
    private $commentRatings;

    public function __construct()
    {
        $this->commentRatings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(): self
    {
        $this->date = new \DateTime("now");

        return $this;
    }

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): self
    {
        $this->campaign = $campaign;

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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection|CommentRating[]
     */
    public function getCommentRatings(): Collection
    {
        return $this->commentRatings;
    }

    public function addCommentRating(CommentRating $commentRating): self
    {
        if (!$this->commentRatings->contains($commentRating)) {
            $this->commentRatings[] = $commentRating;
            $commentRating->setComment($this);
        }

        return $this;
    }

    public function removeCommentRating(CommentRating $commentRating): self
    {
        if ($this->commentRatings->removeElement($commentRating)) {
            // set the owning side to null (unless already changed)
            if ($commentRating->getComment() === $this) {
                $commentRating->setComment(null);
            }
        }

        return $this;
    }
}
