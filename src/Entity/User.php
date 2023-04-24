<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private string $email;

    #[ORM\OneToMany(mappedBy: 'lendedTo', targetEntity: Book::class)]
    private Collection $books;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Book::class, orphanRemoval: true)]
    private Collection $ownedBooks;

    #[ORM\OneToMany(mappedBy: 'requestedBy', targetEntity: BookRequest::class, orphanRemoval: true)]
    private Collection $bookRequests;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserReview::class, orphanRemoval: true)]
    private Collection $userReviews;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    private ?string $region = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $otherContacts = null;

    #[ORM\Column]
    private ?int $isActive = null;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->ownedBooks = new ArrayCollection();
        $this->bookRequests = new ArrayCollection();
        $this->userReviews = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setLendedTo($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getLendedTo() === $this) {
                $book->setLendedTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getOwnedBooks(): Collection
    {
        return $this->ownedBooks;
    }

    public function addOwnedBook(Book $ownedBook): self
    {
        if (!$this->ownedBooks->contains($ownedBook)) {
            $this->ownedBooks->add($ownedBook);
            $ownedBook->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedBook(Book $ownedBook): self
    {
        if ($this->ownedBooks->removeElement($ownedBook)) {
            // set the owning side to null (unless already changed)
            if ($ownedBook->getOwner() === $this) {
                $ownedBook->setOwner(null);
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
            $bookRequest->setRequestedBy($this);
        }

        return $this;
    }

    public function removeBookRequest(BookRequest $bookRequest): self
    {
        if ($this->bookRequests->removeElement($bookRequest)) {
            // set the owning side to null (unless already changed)
            if ($bookRequest->getRequestedBy() === $this) {
                $bookRequest->setRequestedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserReview>
     */
    public function getUserReviews(): Collection
    {
        return $this->userReviews;
    }

    public function addUserReview(UserReview $userReview): self
    {
        if (!$this->userReviews->contains($userReview)) {
            $this->userReviews->add($userReview);
            $userReview->setUser($this);
        }

        return $this;
    }

    public function removeUserReview(UserReview $userReview): self
    {
        if ($this->userReviews->removeElement($userReview)) {
            // set the owning side to null (unless already changed)
            if ($userReview->getUser() === $this) {
                $userReview->setUser(null);
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getOtherContacts(): ?string
    {
        return $this->otherContacts;
    }

    public function setOtherContacts(?string $otherContacts): self
    {
        $this->otherContacts = $otherContacts;

        return $this;
    }

    public function getIsActive(): ?int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
