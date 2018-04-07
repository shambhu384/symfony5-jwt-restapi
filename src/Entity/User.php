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
class User extends BaseUser implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * One user has Many Articles.
     * @OneToMany(targetEntity="Article", mappedBy="user")
     */
    private $articles;

    public function __construct()
    {
        parent::__construct();
        $this->articles = new ArrayCollection();
    }

    public function jsonSerialize() {
        return array(
            "username" => $this->getUsername()
        );
    }
    public function toJson()
    {
        return json_encode([
            'customer' => [
                'email' => $this->email,
            ]
        ]);
    }
}
