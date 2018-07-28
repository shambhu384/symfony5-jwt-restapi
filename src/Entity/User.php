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
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserDevice", mappedBy="userid")
     */
    private $userDevices;

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

    public function getCreated() {
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
}
