<?php

namespace Flower\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * UserGroup
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 * @author Francisco Memoli <fmemoli@flowcode.com.ar>
 */
class UserGroup extends BaseGroup
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct("");
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

}
