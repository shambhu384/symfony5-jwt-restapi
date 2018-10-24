<?php


declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="meeting")
 */
class Meeting
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     * @ORM\Column(type="string", length=100)
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    public $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;


    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="meetings")
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", mappedBy="meetings")
     */
    private $tags;

    /**
     * @ORM\Column(type="integer")
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="organiser_id", referencedColumnName="id")
     */
    private $organiser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     */
    public function setId($id): int
    {
        $this->id = $id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        if ($this->users->contains($user)) {
            return;
        }
        $this->users->add($user);
        $user->setMeeting($this);
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        if (!$this->users->contains($user)) {
            return;
        }
        $this->users->removeElement($user);
        $user->removeMeeting($this);
    }

    /**
     * @param Tag $tag
     */
    public function setTag(Tag $tag)
    {
        if ($this->tags->contains($tag)) {
            return;
        }
        $this->tags->add($tag);
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            return;
        }
        $this->tags->removeElement($tag);
        $tag->removeMeeting($this);
    }

    /**
     * Get datetime.
     *
     * @return DateTime
     */
    public function getDateTime(): ?DateTime
    {
        return $this->datetime;
    }

    /**
     * Set datetime.
     *
     * @param DateTime $datetime
     */
    public function setDateTime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * Get users.
     *
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addMeeting($this);
        }

        return $this;
    }

    public function getOrganiser(): ?int
    {
        return $this->organiser;
    }

    public function setOrganiser(int $organiser): self
    {
        $this->organiser = $organiser;

        return $this;
    }
}
