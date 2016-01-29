<?php

namespace Flower\UserBundle\Service;


use Flower\UserBundle\Model\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\QueryBuilder;

class SecurityGroupService
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

    public function addSecurityGroupFilter(QueryBuilder $queryBuilder, User $user, $alias = null){

        $securityGroups = array();
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
        $users = $this->container->get("user.service.orgposition")->getUpperPositionUsers($user);

        $securityGroups = array();
        foreach($users as $user){
            foreach($user->getSecurityGroups() as $secGroup){
                $securityGroups[] = $secGroup->getId();
            }
        }

        $securityGroupRepo = $this->em->getRepository('FlowerModelBundle:User\SecurityGroup');
        $qb = $securityGroupRepo->createQueryBuilder("sg");
        $qb->where("sg.id IN (:security_groups)")->setParameter("security_groups", $securityGroups);
        return $qb->getQuery()->getResult();
    }

}