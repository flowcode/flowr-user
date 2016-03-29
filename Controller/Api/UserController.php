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


    public function getProfileAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $limit = 10;
        $page = 0;
        $today = new \DateTime();
        $today->sub(new \DateInterval('PT1H'));
        $tomorrow = new \DateTime('tomorrow');
        $tomorrow2 = new \DateTime('tomorrow');
        $tomorrow2->add(new \DateInterval('P1D'));
        $eventtoday = $em->getRepository('FlowerModelBundle:Planner\Event')->findByStartDate($this->getUser(), $today, $tomorrow, $limit, $page * $limit);
        $eventstomorrow = $em->getRepository('FlowerModelBundle:Planner\Event')->findByStartDate($this->getUser(), $tomorrow, $tomorrow2, $limit, $page * $limit);

        $userArr = array(
            "happyname" => $user->getHappyName(),
            "avatarUrl" => $user->getAvatar(),
            "eventstoday" => $eventtoday,
            "eventstomorrow" => $eventstomorrow,
        );

        $view = FOSView::create($userArr, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public', 'api'));
        return $this->handleView($view);
    }
}
