<?php

namespace Flower\UserBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ModelBundle\Entity\User\OrgPosition;
use Flower\ModelBundle\Entity\User\User;
use Flower\UserBundle\Form\Type\OrgPositionType;
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
 * @Route("/admin/orgposition")
 */
class OrgPositionController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="admin_orgposition")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FlowerModelBundle:User\OrgPosition')->childrenHierarchy(null, false, array(), true);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="admin_orgposition_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(User $orgPosition)
    {
        $deleteForm = $this->createDeleteForm($orgPosition->getId(), 'user_delete');

        return array(
            'user' => $orgPosition,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="admin_orgposition_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $orgPosition = new OrgPosition();
        $form = $this->createForm(new OrgPositionType(), $orgPosition);

        return array(
            'user' => $orgPosition,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="admin_orgposition_create")
     * @Method("POST")
     * @Template("FlowerUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $orgPosition = new OrgPosition();
        $form = $this->createForm(new OrgPositionType(), $orgPosition);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orgPosition);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_orgposition'));
        }

        return array(
            'user' => $orgPosition,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_orgposition_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(OrgPosition $orgPosition)
    {
        $editForm = $this->createForm(new OrgPositionType(), $orgPosition, array(
            'action' => $this->generateUrl('admin_orgposition_update', array('id' => $orgPosition->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($orgPosition->getId(), 'user_delete');

        return array(
            'user' => $orgPosition,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/update", name="admin_orgposition_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerUserBundle:User:edit.html.twig")
     */
    public function updateAction(OrgPosition $orgPosition, Request $request)
    {
        $editForm = $this->createForm(new OrgPositionType(), $orgPosition, array(
            'action' => $this->generateUrl('admin_orgposition_update', array('id' => $orgPosition->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_orgposition', array('id' => $orgPosition->getId())));
        }
        $deleteForm = $this->createDeleteForm($orgPosition->getId(), 'user_delete');

        return array(
            'user' => $orgPosition,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_orgposition_sort")
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
     * @Route("/{id}/delete", name="admin_orgposition_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(User $orgPosition, Request $request)
    {
        $form = $this->createDeleteForm($orgPosition->getId(), 'user_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orgPosition);
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
    

}
