<?php // src/Entity/Article.php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity
 * @ORM\Table(name="article")
 */
class Article {

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
     * Many Features have One Product.
     *
     * @ManyToOne(targetEntity="User", inversedBy="articles")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function setFromJson($json) {
        $json = json_decode($json);
        $this->name = $json->name;
        $this->description = $json->description;
    }
}
