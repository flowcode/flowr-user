<?php

namespace Flower\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;

/**
 * NotificationController
 */
class NotificationController extends FOSRestController
{
    public function myNotificationsAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository("FlowerModelBundle:User\UserNotification")->findBy(array('user' => $user), array('created' => 'DESC'), 10, 0);
        
        $view = FOSView::create($notifications, Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }
}
