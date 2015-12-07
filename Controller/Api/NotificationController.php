<?php

namespace Flower\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ModelBundle\Entity\User\UserNotification;

/**
 * NotificationController
 */
class NotificationController extends FOSRestController
{
    public function myNotificationsAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository("FlowerModelBundle:User\UserNotification")->findBy(array('user' => $user), array('viewed' => 'ASC', 'created' => 'DESC'), 10, 0);

        $view = FOSView::create($notifications, Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }

    public function unreadNotificationsCountAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $count = $em->getRepository("FlowerModelBundle:User\UserNotification")->getUnreadsCount($user->getId());

        $view = FOSView::create(array("count" => $count), Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }

    public function setViewedAction(Request $request, UserNotification $userNotification)
    {
        $em = $this->getDoctrine()->getManager();
        $userNotification->setViewed();
        $em->flush();

        $view = FOSView::create(array(), Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }
}
