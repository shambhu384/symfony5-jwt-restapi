<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Article;
use Doctrine\ORM\Mapping\OneToMany;

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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->meetings = new ArrayCollection();
        $this->created = new \DateTime("now");
    }

    /**
     * Get fullName.
     *
     * @return fullName.
     */
    public function getFullName()
    {
        return $this->fullname;
    }

    /**
     * Set fullName.
     *
     * @param fullName the value to set.
     */
    public function setFullName($fullname)
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
}
