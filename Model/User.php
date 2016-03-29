<?php

namespace Flower\UserBundle\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\Serializer\Annotation\Groups;

/**
 * User
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 * @author Francisco Memoli <fmemoli@flowcode.com.ar>
 */
abstract class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"kanban", "full", "public"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="initials", type="string", length=255, nullable=true)
     * @Groups({"kanban", "full", "public"})
     */
    protected $initials;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     * @Groups({"kanban", "full", "public"})
     */
    protected $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     * @Groups({"kanban", "full", "public"})
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     * @Groups({"kanban", "full", "public"})
     */
    protected $lastname;

    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your invitation is wrong", groups={"Registration"})
     */
    protected $invitation;
    /**
     * @ORM\ManyToMany(targetEntity="UserGroup")
     * @ORM\JoinTable(name="users_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Marketing\ContactList", mappedBy="users")
     */
    private $contactLists;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\OrgPosition")
     * @JoinColumn(name="position_id", referencedColumnName="id")
     * */
    protected $orgPosition;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\User\SecurityGroup")
     * @JoinTable(name="users_user_security_groups",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="security_group_id", referencedColumnName="id")}
     *      )
     */
    protected $securityGroups;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=255, nullable=true)
     */
    protected $apiToken;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

    public function __construct()
    {
        parent::__construct();
        $this->securityGroups = new ArrayCollection();
        $this->contactLists = new ArrayCollection();
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return User
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

    /**
     * Set updated
     *
     * @param DateTime $updated
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
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

    public function getHappyName()
    {
        if (!is_null($this->firstname) && !is_null($this->lastname)) {
            return ucfirst($this->firstname) . " " . ucfirst($this->lastname);
        } elseif (!is_null($this->firstname)) {
            return ucfirst($this->firstname);
        } else {
            return $this->username;
        }
    }

    public function setInvitation(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

    public function __toString()
    {
        return $this->getHappyName();
    }

    /**
     * Add groups
     *
     * @param GroupInterface $groups
     * @return User
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param GroupInterface $groups
     */
    public function removeGroup(GroupInterface $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     * @return User
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Get initials
     * @return String
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * Set initials
     * @return String
     */
    public function setInitials($initials)
    {
        $this->initials = $initials;
        return $this;
    }

    /**
     * Set orgPosition
     *
     * @param \Flower\ModelBundle\Entity\User\OrgPosition $orgposition
     * @return User
     */
    public function setOrgPosition(\Flower\ModelBundle\Entity\User\OrgPosition $orgposition = null)
    {
        $this->orgPosition = $orgposition;
        return $this;
    }

    /**
     * Get orgPosition
     *
     * @return \Flower\ModelBundle\Entity\User\OrgPosition
     */
    public function getOrgPosition()
    {
        return $this->orgPosition;
    }

    /**
     * Get avatar
     * @return String
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set avatar
     * @return String
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Add securityGroup
     *
     * @param \Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups
     * @return User
     */
    public function addSecurityGroup(\Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups)
    {
        $this->securityGroups[] = $securityGroups;

        return $this;
    }

    /**
     * Remove securityGroups
     *
     * @param \Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups
     */
    public function removeSecurityGroup(\Flower\ModelBundle\Entity\User\SecurityGroup $securityGroups)
    {
        $this->securityGroups->removeElement($securityGroups);
    }

    /**
     * Get securityGroups
     *
     * @return Collection
     */
    public function getSecurityGroups()
    {
        return $this->securityGroups;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

}
