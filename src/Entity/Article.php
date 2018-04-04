<?php // src/Entity/Article.php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
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

    public function setFromJson($json) {
        $json = json_decode($json);
        $this->name = $json->name;
        $this->description = $json->description;
    }
}
