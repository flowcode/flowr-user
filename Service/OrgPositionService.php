<?php

namespace Flower\UserBundle\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Flower\ModelBundle\Entity\User\User;

/**
 * Description of OrgPositionService
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 * @author Francisco Memoli <fmemoli@flowcode.com.ar>
 */
class OrgPositionService
{

    /**
     * @var Container
     */
    private $container;

    public function __construct(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine.orm.entity_manager");
    }

    /**
     * Add filters depending on the user organization position.
     *
     * @param QueryBuilder $queryBuilder
     * @param User $user
     * @return QueryBuilder
     */
    public function addPositionFilter(QueryBuilder $queryBuilder, User $user, $alias = null)
    {
        $userOrgPosition = $user->getOrgPosition();

        /* get lower org positions */
        $orgPositionRepo = $this->em->getRepository('FlowerModelBundle:User\OrgPosition');
        $userRepo = $this->em->getRepository('FlowerModelBundle:User\User');
        $childrens = $orgPositionRepo->getChildren($userOrgPosition, false, null, null, true);


        $lowerPositions = array();
        foreach ($childrens as $lowerPosition) {
            $lowerPositions[] = $lowerPosition->getId();
        }

        /* get users with lower org positions */
        $lowerUsers = $userRepo->findByOrgPositions($lowerPositions);
        $lowerPositionUsers = array();
        foreach ($lowerUsers as $lowerUser) {
            $lowerPositionUsers[] = $lowerUser->getId();
        }

        $aliasFix = "";
        if ($alias) {
            $aliasFix .= $alias . ".";
        }

        $queryBuilder->andWhere($aliasFix . 'assignee IN (:users)')->setParameter("users", $lowerPositionUsers);

        return $queryBuilder;
    }

}
