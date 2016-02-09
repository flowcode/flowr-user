<?php

namespace Flower\UserBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ModelBundle\Entity\User\OrganizationSetting;
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
 * @Route("/organization")
 */
class OrganizationController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/logo", name="organization_logo")
     * @Method("GET")
     * @Template()
     */
    public function logoAction($size = null)
    {
        $logo = $this->get('user.service.organization_setting')->getValue(OrganizationSetting::logo);
        if($logo){
            $logo = 'uploads' . $logo->getValue();
        }
        return array(
            "size" => $size,
            "logo_path" => $logo ? $logo : null,
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
