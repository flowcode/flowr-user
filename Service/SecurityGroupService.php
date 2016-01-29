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

    public function addSecurityGroupFilter(QueryBuilder $queryBuilder, User $user, $alias = null){

        $securityGroups = array();
        $defaultSecGroup = $this->getDefaultForUser($user);
        $securityGroups[] = $defaultSecGroup->getId();

        foreach($user->getSecurityGroups() as $secGroup){
            if(!in_array($secGroup->getId(), $securityGroups)){
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
            ->setParameter("security_groups", $securityGroups)
        ;

        return $queryBuilder;
    }

    public function getParentsGroups(User $user)
    {
        $users = $this->orgPositionService->getUpperPositionUsers($user);

        $securityGroups = array();
        foreach($users as $user){
            foreach($user->getSecurityGroups() as $secGroup){
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
    public function getDefaultForUser(User $user){
        return $this->securityGroupRepository->getOneByAssignee($user->getId());
    }

}