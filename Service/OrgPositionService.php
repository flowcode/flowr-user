<?php

namespace Flower\UserBundle\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Flower\ModelBundle\Entity\User\User;
use Doctrine\ORM\EntityRepository;

/**
 * Description of OrgPositionService
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 * @author Francisco Memoli <fmemoli@flowcode.com.ar>
 */
class OrgPositionService
{

    /**
     * @var EntityRepository
     */
    private $userRepository;

    /**
     * @var EntityRepository
     */
    private $orgPositionRepository;

    public function __construct(EntityRepository $userRepository, EntityRepository $orgPositionRepository)
    {
        $this->userRepository = $userRepository;
        $this->orgPositionRepository = $orgPositionRepository;
    }

    public function getLowerPositionUsers(User $user, $includeUserPosition = false)
    {
        $userOrgPosition = $user->getOrgPosition();

        /* get lower org positions */
        $childrens = $this->orgPositionRepository->getChildren($userOrgPosition);
        $lowerPositions = array();

        if($includeUserPosition){
            $lowerPositions[] = $userOrgPosition->getId();
        }

        foreach ($childrens as $lowerPosition) {
            $lowerPositions[] = $lowerPosition->getId();
        }

        /* get users with lower org positions */
        $lowerUsers = $this->userRepository->findByOrgPositions($lowerPositions);

        return $lowerUsers;
    }

    /**
     * Get the upper positions.
     *
     * @param User $user
     * @return array
     */
    public function getUpperPositions(User $user)
    {
        $userOrgPosition = $user->getOrgPosition();
        $parents = array();
        $isParent = false;
        while(!$isParent){
            $parent = $userOrgPosition->getParent();
            if(is_null($parent)){
                $isParent = true;
            }else{
                $parents[] = $parent;
            }
            $userOrgPosition = $parent;
        }
        return $parents;
    }

    public function getUpperPositionUsers(User $user)
    {
        /* get upper org positions */
        $parents = $this->getUpperPositions($user);

        $upperPositions = array();
        foreach ($parents as $upperPosition) {
            $upperPositions[] = $upperPosition->getId();
        }

        /* get users with lower org positions */
        $lowerUsers = $this->userRepository->findByOrgPositions($upperPositions);

        return $lowerUsers;
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
        $lowerPositions = $this->orgPositionRepository->getChildren($userOrgPosition);
        $lowerPositionsIds = array();
        foreach ($lowerPositions as $position) {
            $lowerPositionsIds[] = $position->getId();
        }
        $aliasFix = "";
        if ($alias) {
            $aliasFix .= $alias . ".";
        }
        $queryBuilder->andWhere($aliasFix . 'orgPosition IN (:position)')->setParameter("position", $lowerPositionsIds);

        return $queryBuilder;
    }

}
