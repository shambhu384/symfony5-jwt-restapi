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
 * @ORM\HasLifecycleCallbacks()
 *
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
     * @ORM\OneToMany(targetEntity="App\Entity\Meeting", mappedBy="organiser")
     */
    private $meetings;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->created = new \DateTime("now");
        $this->userDevices = new ArrayCollection();
        $this->meetings = new ArrayCollection();
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

    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * @return Collection|Meeting[]
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings[] = $meeting;
            $meeting->setOrganiser($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        if ($this->meetings->contains($meeting)) {
            $this->meetings->removeElement($meeting);
            // set the owning side to null (unless already changed)
            if ($meeting->getOrganiser() === $this) {
                $meeting->setOrganiser(null);
            }
        }

        return $this;
    }
}
