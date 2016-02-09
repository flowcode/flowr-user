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
use Symfony\Component\HttpFoundation\JsonResponse;
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
        return array(
            "size" => $size,
            "logo_path" => $logo,
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
        $qb->where("u.enabled = 1");
        //$this->addQueryBuilderSort($qb, 'user');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Lists all User entities.
     *
     * @Route("/settings", name="organization_settings")
     * @Method("GET")
     * @Template()
     */
    public function settingsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('FlowerModelBundle:User\OrganizationSetting')->findAll();

        return array(
            "settings" => $settings,
        );
    }

    /**
     * Update setting.
     *
     * @Route("/settings", name="organization_settings_update")
     * @Method("POST")
     */
    public function updateAction(Request $request)
    {

        $settingType = $request->get("type");
        $em = $this->getDoctrine()->getManager();
        $currSetting = $em->getRepository('FlowerModelBundle:User\OrganizationSetting')->find($request->get("id"));

        switch ($settingType) {
            case OrganizationSetting::type_string:
                $currSetting->setValue($request->get("value"));
                break;
            case OrganizationSetting::type_file_image:
                if (isset($_FILES[0])) {
                    $file = $_FILES[0];

                    $uploadBaseDir = $this->container->getParameter("uploads_base_dir");
                    $uploadDir = $this->container->getParameter("organization_dir");

                    $imageName = basename($file['name']);

                    if (move_uploaded_file($file['tmp_name'], $uploadBaseDir . $uploadDir . $imageName)) {
                        $currSetting->setValue($uploadDir . $imageName);
                    } else {
                        return new JsonResponse(null, 500);
                    }

                }
                break;
        }

        $em->flush();

        return new JsonResponse(null, 200);
    }


}
