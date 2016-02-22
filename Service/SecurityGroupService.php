<?php

namespace Flower\UserBundle\Service;


use Flower\ModelBundle\Entity\User\SecurityGroup;
use Flower\UserBundle\Model\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;


class SecurityGroupService
{

    private $securityGroupRepository;
    private $orgPositionService;

    public function __construct(EntityRepository $securityGroupRepository, $orgPositionService)
    {
        $this->securityGroupRepository = $securityGroupRepository;
        $this->orgPositionService = $orgPositionService;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param User $user
     * @param null $alias
     * @return QueryBuilder
     */
    public function addSecurityGroupFilter(QueryBuilder $queryBuilder, User $user, $alias = null)
    {

        $securityGroups = array();
        $defaultSecGroup = $this->getDefaultForUser($user);
        $securityGroups[] = $defaultSecGroup->getId();

        foreach ($user->getSecurityGroups() as $secGroup) {
            if (!in_array($secGroup->getId(), $securityGroups)) {
                $securityGroups[] = $secGroup->getId();
            }
        }

        $aliasFix = "";
        if ($alias) {
            $aliasFix .= $alias . ".";
        }

        $queryBuilder
            ->join($aliasFix . 'securityGroups', 'secgroup')
            ->andWhere('secgroup.id IN (:security_groups)')
            ->setParameter("security_groups", $securityGroups);

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param User $user
     * @param null $alias
     * @return QueryBuilder
     */
    public function addLowerSecurityGroupsFilter(QueryBuilder $queryBuilder, User $user, $alias = null)
    {

        $securityGroups = array();
        $defaultSecGroup = $this->getDefaultForUser($user);
        $securityGroups[] = $defaultSecGroup->getId();

        foreach ($user->getSecurityGroups() as $secGroup) {
            if (!in_array($secGroup->getId(), $securityGroups)) {
                $securityGroups[] = $secGroup->getId();
            }
        }

        $lowerSecGroupIds = $this->getLowerGroupsIds($user);
        foreach ($lowerSecGroupIds as $secGroupLow) {
            if (!in_array($secGroupLow, $securityGroups)) {
                $securityGroups[] = $secGroupLow;
            }
        }

        $aliasFix = "";
        if ($alias) {
            $aliasFix .= $alias . ".";
        }

        $queryBuilder
            ->join($aliasFix . 'securityGroups', 'secgroup')
            ->andWhere('secgroup.id IN (:security_groups)')
            ->setParameter("security_groups", $securityGroups);

        return $queryBuilder;
    }

    /**
     * @param User $user
     * @param bool $withMe
     * @return array
     */
    public function getLowerGroupsIds(User $user, $withMe = true)
    {
        $lowerUsers = $this->orgPositionService->getLowerPositionUsers($user, $withMe);
        $securityGroups = array();
        foreach ($lowerUsers as $lowerUser) {
            foreach ($lowerUser->getSecurityGroups() as $secGroup) {
                $securityGroups[] = $secGroup->getId();
            }
        }
        foreach ($user->getSecurityGroups() as $secGroup) {
            $securityGroups[] = $secGroup->getId();
        }
        return $securityGroups;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getLowerGroups(User $user)
    {
        $securityGroups = $this->getLowerGroupsIds($user);
        $qb = $this->securityGroupRepository->createQueryBuilder("sg");
        $qb->where("sg.id IN (:security_groups)")->setParameter("security_groups", $securityGroups);
        return $qb->getQuery()->getResult();

    }

    /**
     * @param User $user
     * @return array
     */
    public function getParentsGroups(User $user)
    {
        $users = $this->orgPositionService->getUpperPositionUsers($user);

        $securityGroups = array();
        foreach ($users as $user) {
            foreach ($user->getSecurityGroups() as $secGroup) {
                $securityGroups[] = $secGroup->getId();
            }
        }

        $qb = $this->securityGroupRepository->createQueryBuilder("sg");
        $qb->where("sg.id IN (:security_groups)")->setParameter("security_groups", $securityGroups);
        return $qb->getQuery()->getResult();
    }


    /**
     * Get the default security group.
     *
     * @param User $user
     * @return null|SecurityGroup
     */
    public function getDefaultForUser(User $user)
    {
        return $this->securityGroupRepository->getOneByAssignee($user->getId());
    }

    /**
     * @param $user
     * @param $entity
     * @return bool
     */
    public function userCanSeeEntity($user, $entity)
    {
        $userGroups = $this->getLowerGroups($user, false);
        $canSee = false;
        foreach ($userGroups as $group) {
            foreach ($entity->getSecurityGroups() as $entityGroup) {
                if ($group->getId() == $entityGroup->getId()) {
                    $canSee = true;
                    break;
                }
            }
            if ($canSee) {
                break;
            }
        }
        return $canSee;
    }

}