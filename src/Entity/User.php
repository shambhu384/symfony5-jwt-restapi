<?php

declare(strict_types=1);


namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Article;
use Doctrine\ORM\Mapping\OneToMany;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @SWG\Property(type="string", maxLength=255)
     */
    protected $fullname;

    /**
     * @var \Doctrine\Common\Collections\Collection|Meeting[]
     *
     * @ORM\ManyToMany(targetEntity="Meeting", inversedBy="users")
     * @ORM\JoinTable(
     *  name="user_meeting",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="meeting_id", referencedColumnName="id")
     *  }
     * )
     */
    protected $meetings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserDevice", mappedBy="userid")
     */
    private $userDevices;

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
        parent::__construct();
        $this->meetings = new ArrayCollection();
        $this->created = new \DateTime("now");
        $this->userDevices = new ArrayCollection();
    }

    /**
     * Get fullName.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullname;
    }

    /**
     * Set fullName.
     *
     * @param string $fullname
     */
    public function setFullName(string $fullname)
    {
        $this->fullname = $fullname;
    }

    public function getCreated()
    {
        return $this->created;
    }
    /**
     * @param Meeting $meeting
     */
    public function setMeeting(Meeting $meeting)
    {
        // avoid duplicates
        if ($this->meetings->contains($meeting)) {
            return;
        }
        $this->meetings->add($meeting);
        $meeting->setUser($this);
    }
    /**
     * @param Meeting $meeting
     */
    public function removeMeeting(Meeting $meeting)
    {
        // avoid duplicates
        if (!$this->meetings->contains($meeting)) {
            return;
        }

        $this->meetings->removeElement($meeting);
        $meeting->removeUser($this);
    }

    /**
     * @return Collection|UserDevice[]
     */
    public function getUserDevices(): Collection
    {
        return $this->userDevices;
    }

    public function addUserDevice(UserDevice $userDevice): self
    {
        if (!$this->userDevices->contains($userDevice)) {
            $this->userDevices[] = $userDevice;
            $userDevice->setUserid($this);
        }
        return $this;
    }

    /**
     * Remove user device
     *
     * @param UserDevice $userDevice
     * @return self
     */
    public function removeUserDevice(UserDevice $userDevice): self
    {
        if ($this->userDevices->contains($userDevice)) {
            $this->userDevices->removeElement($userDevice);
            // set the owning side to null (unless already changed)
            if ($userDevice->getUserid() === $this) {
                $userDevice->setUserid(null);
            }
        }
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
