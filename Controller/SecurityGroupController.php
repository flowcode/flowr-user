<?php

namespace Flower\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\User\SecurityGroup;
use Flower\UserBundle\Form\Type\SecurityGroupType;

/**
 * SecurityGroup controller.
 *
 * @Route("/admin/securityGroup")
 */
class SecurityGroupController extends Controller
{

    /**
     * Lists all SecurityGroup entities.
     *
     * @Route("/", name="securityGroup")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:User\SecurityGroup')->createQueryBuilder('u');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a SecurityGroup entity.
     *
     * @Route("/{id}/show", name="securityGroup_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(SecurityGroup $securityGroup)
    {
        $deleteForm = $this->createDeleteForm($securityGroup->getId(), 'securityGroup_delete');



        return array(
            'securityGroup' => $securityGroup,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new SecurityGroup entity.
     *
     * @Route("/new", name="securityGroup_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $securityGroup = new SecurityGroup();
        $form = $this->createForm($this->get("form.type.securityGroup"), $securityGroup);

        return array(
            'securityGroup' => $securityGroup,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new SecurityGroup entity.
     *
     * @Route("/create", name="securityGroup_create")
     * @Method("POST")
     * @Template("FlowerUserBundle:SecurityGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $securityGroup = new SecurityGroup();
        $form = $this->createForm($this->get("form.type.securityGroup"), $securityGroup);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($securityGroup);
            $em->flush();

            return $this->redirect($this->generateUrl('securityGroup'));
        }

        return array(
            'securityGroup' => $securityGroup,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SecurityGroup entity.
     *
     * @Route("/{id}/edit", name="securityGroup_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(SecurityGroup $securityGroup)
    {
        
        $editForm = $this->createEditForm($securityGroup);
        
        $deleteForm = $this->createDeleteForm($securityGroup->getId(), 'securityGroup_delete');

        return array(
            'securityGroup' => $securityGroup,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing SecurityGroup entity.
     *
     * @Route("/{id}/update", name="securityGroup_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerUserBundle:SecurityGroup:edit.html.twig")
     */
    public function updateAction(SecurityGroup $securityGroup, Request $request)
    {
        $editForm = $this->createEditForm($securityGroup);
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('securityGroup_show', array('id' => $securityGroup->getId())));
        }
        $deleteForm = $this->createDeleteForm($securityGroup->getId(), 'securityGroup_delete');

        return array(
            'securityGroup' => $securityGroup,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a SecurityGroup entity.
     *
     * @Route("/{id}/delete", name="securityGroup_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(SecurityGroup $securityGroup, Request $request)
    {
        $form = $this->createDeleteForm($securityGroup->getId(), 'securityGroup_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($securityGroup);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('securityGroup'));
    }

    /**
     * Creates a form to edit a SecurityGroup entity.
     *
     * @param SecurityGroup $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SecurityGroup $entity)
    {
        $form = $this->createForm($this->get("form.type.securityGroup"), $entity, array(
            'action' => $this->generateUrl('securityGroup_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

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
