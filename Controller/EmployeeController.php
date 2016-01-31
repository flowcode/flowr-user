<?php

namespace Flower\UserBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ModelBundle\Entity\User\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Flower\UserBundle\Form\Type\UserType;
use Flower\UserBundle\Form\Type\UserProfileType;

/**
 * User controller.
 *
 * @Route("/employee")
 */
class EmployeeController extends Controller
{

    /**
     * Finds and displays a User entity.
     *
     * @Route("/profile/{username}", name="employee_profile_public")
     * @Method("GET")
     * @Template()
     */
    public function publicProfileAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FlowerModelBundle:User\User')->findOneBy(array("username" => $username));

        $activityFeed = $this->get('board.service.history')->getUserActivity($user);

        return array(
            'user' => $user,
            'feed' => $activityFeed,
        );
    }

    /**
     * Lists all User entities.
     *
     * @Route("/", name="employee")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:User\User')->createQueryBuilder('u');
        //$this->addQueryBuilderSort($qb, 'user');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }



}
