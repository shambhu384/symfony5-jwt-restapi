<?php


declare(strict_types=1);

namespace App\Entity;

use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass="App\Repository\MeetingRepository")
 * @ORM\Table(name="meeting")
 * @ORM\HasLifecycleCallbacks()
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
     * @Groups("user")
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
      * @ORM\Column(type="datetime")
      */
    protected $createdAt;

     /**
      * @ORM\Column(type="datetime")
      */
    protected $updatedAt;

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

    /**
     * Pre persist event listener
     *
     * @ORM\PrePersist
     */
    public function beforeSave()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }
    /**
     * Pre update event handler
     *
     * @ORM\PreUpdate
     */
    public function doPreUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * Get created date/time
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * @param \DateTime
     *
     * @return Account
     */
    public function setCreatedAt($created)
    {
        $this->createdAt = $created;
        return $this;
    }
    /**
     * Get last update date/time
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * @param \DateTime
     *
     * @return Account
     */
    public function setUpdatedAt($updated)
    {
        $this->updatedAt = $updated;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}
