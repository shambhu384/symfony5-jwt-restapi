<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="meeting")
 */
class Meeting {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $name;

    /**
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
     * Constructor
     */
    public function __construct()
    {
		$this->users = new ArrayCollection();
    }


	/**
     * Get id.
     *
	 * @return id.
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set id.
	 *
	 * @param id the value to set.
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Get name.
	 *
	 * @return name.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set name.
	 *
	 * @param name the value to set.
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get description.
	 *
	 * @return description.
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set description.
	 *
	 * @param description the value to set.
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
     * Get datetime.
     *
     * @return datetime.
     */
    public function getDateTime()
    {
        return $this->datetime;
    }

    /**
     * Set datetime.
     *
     * @param datetime the value to set.
     */
    public function setDateTime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * Get users.
     *
     * @return users.
     */
    public function getUsers()
    {
        return $this->users;
    }

}
