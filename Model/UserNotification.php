<?php

namespace Flower\UserBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;


/**
 * Description of Notification.
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
abstract class UserNotification
{

    const type_task = 'task';
    const type_event = 'event';
    const type_project = 'project';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", nullable=true)
     */
    protected $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="was_seen", type="boolean")
     */
    protected $viewed;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;



    public function __construct()
    {
        $this->viewed = false;
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

    /**
     * Set type
     *
     * @param string $type
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \Flower\ModelBundle\Entity\User\User $user
     * @return Task
     */
    public function setUser(\Flower\ModelBundle\Entity\User\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return User
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return User
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }


    public function isViewed()
    {
        return $this->viewed;
    }

    public function setViewed()
    {
        $this->sent = true;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return UserNotification
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

}
