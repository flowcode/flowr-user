<?php

namespace Flower\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\User\UserGroup;
use Flower\UserBundle\Form\Type\UserGroupType;

/**
 * UserGroup controller.
 *
 * @Route("/usergroup")
 */
class UserGroupController extends Controller
{

    /**
     * Lists all UserGroup entities.
     *
     * @Route("/", name="usergroup")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:User\UserGroup')->createQueryBuilder('u');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a UserGroup entity.
     *
     * @Route("/{id}/show", name="usergroup_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(UserGroup $usergroup)
    {
        $deleteForm = $this->createDeleteForm($usergroup->getId(), 'usergroup_delete');

        return array(
            'usergroup' => $usergroup,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new UserGroup entity.
     *
     * @Route("/new", name="usergroup_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $usergroup = new UserGroup();
        $form = $this->createForm(new UserGroupType(), $usergroup);

        return array(
            'usergroup' => $usergroup,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new UserGroup entity.
     *
     * @Route("/create", name="usergroup_create")
     * @Method("POST")
     * @Template("FlowerUserBundle:UserGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $usergroup = new UserGroup();
        $form = $this->createForm(new UserGroupType(), $usergroup);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($usergroup);
            $em->flush();

            return $this->redirect($this->generateUrl('usergroup_show', array('id' => $usergroup->getId())));
        }

        return array(
            'usergroup' => $usergroup,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing UserGroup entity.
     *
     * @Route("/{id}/edit", name="usergroup_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(UserGroup $usergroup)
    {
        
        $editForm = $this->createEditForm($usergroup);
        
        $deleteForm = $this->createDeleteForm($usergroup->getId(), 'usergroup_delete');

        return array(
            'usergroup' => $usergroup,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing UserGroup entity.
     *
     * @Route("/{id}/update", name="usergroup_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerUserBundle:UserGroup:edit.html.twig")
     */
    public function updateAction(UserGroup $usergroup, Request $request)
    {
        $editForm = $this->createEditForm($usergroup);
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('usergroup_show', array('id' => $usergroup->getId())));
        }
        $deleteForm = $this->createDeleteForm($usergroup->getId(), 'usergroup_delete');

        return array(
            'usergroup' => $usergroup,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a UserGroup entity.
     *
     * @Route("/{id}/delete", name="usergroup_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(UserGroup $usergroup, Request $request)
    {
        $form = $this->createDeleteForm($usergroup->getId(), 'usergroup_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($usergroup);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usergroup'));
    }

    /**
     * Creates a form to edit a UserGroup entity.
     *
     * @param UserGroup $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(UserGroup $entity)
    {
        $form = $this->createForm(new UserGroupType(), $entity, array(
            'action' => $this->generateUrl('usergroup_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('roles', 'choice', array(
            'choices' => $this->get('flower.security.roles')->getRoles(),
            'multiple' => true,
            'label' => 'Roles'
                )
        );

        return $form;
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl($route, array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
