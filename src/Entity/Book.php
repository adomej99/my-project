<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Title = null;

    #[ORM\Column(length: 255)]
    private ?string $Author = null;

    #[ORM\Column(length: 255)]
    private ?string $Thumbnail = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?User $lendedTo = null;

    #[ORM\Column]
    private ?bool $available = false;

    #[ORM\ManyToOne(inversedBy: 'ownedBooks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookHistory::class, orphanRemoval: true)]
    private Collection $bookHistories;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookRequest::class, orphanRemoval: true)]
    private Collection $bookRequests;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookReview::class, orphanRemoval: true)]
    private Collection $bookReviews;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $image = null;

    public function __construct()
    {
        $this->bookHistories = new ArrayCollection();
        $this->bookRequests = new ArrayCollection();
        $this->bookReviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->Author;
    }

    public function setAuthor(string $Author): self
    {
        $this->Author = $Author;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->Thumbnail;
    }

    public function setThumbnail(string $Thumbnail): self
    {
        $this->Thumbnail = $Thumbnail;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLendedTo(): ?User
    {
        return $this->lendedTo;
    }

    public function setLendedTo(?User $lendedTo): self
    {
        $this->lendedTo = $lendedTo;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
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

    /**
     * @return Collection<int, BookHistory>
     */
    public function getBookHistories(): Collection
    {
        return $this->bookHistories;
    }

    public function addBookHistory(BookHistory $bookHistory): self
    {
        if (!$this->bookHistories->contains($bookHistory)) {
            $this->bookHistories->add($bookHistory);
            $bookHistory->setBook($this);
        }

        return $this;
    }

    public function removeBookHistory(BookHistory $bookHistory): self
    {
        if ($this->bookHistories->removeElement($bookHistory)) {
            // set the owning side to null (unless already changed)
            if ($bookHistory->getBook() === $this) {
                $bookHistory->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BookRequest>
     */
    public function getBookRequests(): Collection
    {
        return $this->bookRequests;
    }

    public function addBookRequest(BookRequest $bookRequest): self
    {
        if (!$this->bookRequests->contains($bookRequest)) {
            $this->bookRequests->add($bookRequest);
            $bookRequest->setBook($this);
        }

        return $this;
    }

    public function removeBookRequest(BookRequest $bookRequest): self
    {
        if ($this->bookRequests->removeElement($bookRequest)) {
            // set the owning side to null (unless already changed)
            if ($bookRequest->getBook() === $this) {
                $bookRequest->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BookReview>
     */
    public function getBookReviews(): Collection
    {
        return $this->bookReviews;
    }

    public function addBookReview(BookReview $bookReview): self
    {
        if (!$this->bookReviews->contains($bookReview)) {
            $this->bookReviews->add($bookReview);
            $bookReview->setBook($this);
        }

        return $this;
    }

    public function removeBookReview(BookReview $bookReview): self
    {
        if ($this->bookReviews->removeElement($bookReview)) {
            // set the owning side to null (unless already changed)
            if ($bookReview->getBook() === $this) {
                $bookReview->setBook(null);
            }
        }

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }
}
