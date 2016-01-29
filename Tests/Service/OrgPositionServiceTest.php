<?php

use Doctrine\ORM\EntityRepository;
use Flower\ModelBundle\Entity\User\OrgPosition;
use Flower\ModelBundle\Entity\User\User;
use Flower\UserBundle\Service\OrgPositionService;

/**
 * Class OrgPositionServiceTest
 */
class OrgPositionServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Flower\UserBundle\Service\OrgPositionService
     */
    private $entity;


    public function setUp()
    {


    }


    public function testGetUpperPositions()
    {

        /* positions */
        $orgPositionRoot = new OrgPosition();

        $orgPositionParent = new OrgPosition();
        $orgPositionParent->setParent($orgPositionRoot);

        $orgPositionChild = new OrgPosition();
        $orgPositionChild->setParent($orgPositionParent);

        $orgPositionChildChild = new OrgPosition();
        $orgPositionChildChild->setParent($orgPositionChild);

        /* user */
        $user = new User();
        $user->setOrgPosition($orgPositionChildChild);

        $userRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orgPositionRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entity = new OrgPositionService($userRepository, $orgPositionRepository);

        $positions = $this->entity->getUpperPositions($user);

        $this->assertEquals(3, count($positions));
    }
}
