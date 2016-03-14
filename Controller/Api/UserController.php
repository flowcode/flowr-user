<?php

namespace Flower\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\UserBundle\Model\User;

class UserController extends FOSRestController
{
    public function findAvailablesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('FlowerModelBundle:User\User')->findBy(array("enabled" => true));

        $view = FOSView::create($users, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('kanban'));
        return $this->handleView($view);
    }
}
