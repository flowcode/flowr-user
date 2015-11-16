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
 * @Route("/admin/user")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:User\User')->createQueryBuilder('u');
        $this->addQueryBuilderSort($qb, 'user');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="user_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user->getId(), 'user_delete');

        return array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="user_create")
     * @Method("POST")
     * @Template("FlowerUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        if ($form->handleRequest($request)->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user'));
        }

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(User $user)
    {
        $editForm = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('user_update', array('id' => $user->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($user->getId(), 'user_delete');

        return array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/update", name="user_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerUserBundle:User:edit.html.twig")
     */
    public function updateAction(User $user, Request $request)
    {
        $editForm = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('user_update', array('id' => $user->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }
        $deleteForm = $this->createDeleteForm($user->getId(), 'user_delete');

        return array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="user_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('user', $field, $type);

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="user_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(User $user, Request $request)
    {
        $form = $this->createDeleteForm($user->getId(), 'user_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl($route, array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }



    /**
     * Lists all User entities.
     *
     * @Route("/invite", name="user_invite")
     * @Method("GET")
     * @Template()
     */
    public function inviteAction()
    {
        $error = null;
        return array(
            'email' => null,
            'error' => $error,
        );
    }

    /**
     * Lists all User entities.
     *
     * @Route("/send_invite", name="user_invite_send")
     * @Method("POST")
     * @Template("FlowerUserBundle:User:invite.html.twig")
     */
    public function sendInviteAction(Request $request)
    {
        $email = $request->get("email");
        $error = null;

        $em = $this->getDoctrine()->getManager();
        $alreadyEmail = $em->getRepository("FlowerModelBundle:User\User")->findOneBy(array("email" => $email));

        if ($alreadyEmail) {
            $error = array("message" => "El usuario ya existe.");
        } else {
            $invite = new \Flower\ModelBundle\Entity\User\Invitation();
            $invite->setEmail($email);

            /* launch notificacion */
            $this->get("flower.notification")->notificateInvite($invite);

            $invite->send();

            $em->persist($invite);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Invitation sent!');
            return $this->redirect($this->generateUrl('user_invite'));
        }

        return array(
            'error' => $error,
            'email' => $email,
        );
    }

}
