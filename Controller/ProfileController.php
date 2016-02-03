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
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Lists all User entities.
     *
     * @Route("/avatar", name="user_avatar")
     * @Method("GET")
     * @Template("FlowerUserBundle:User:avatar.html.twig")
     */
    public function avatarAction(User $user = null, $size = null)
    {
        $gravatarUrl = "http://www.gravatar.com/avatar/";

        $external = false;
        if (is_null($user)) {
            $user = $this->getUser();
        }

        /* check user settings */
        if(strlen($user->getAvatar()) > 0){
            $avatarUrl = $user->getAvatar();
        }else{
            $external = true;
            $hash = md5(strtolower(trim($user->getEmail())));
            $avatarUrl = $gravatarUrl.$hash;
        }

        if(is_null($size)){
            $size = 'small';
        }

        return array(
            'external' => $external,
            'size' => $size,
            'avatarUrl' => $avatarUrl,
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/p/{username}", name="user_profile_public")
     * @Method("GET")
     * @Template()
     */
    public function publicProfileAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FlowerModelBundle:User\User')->findOneBy(array("username" => $username));

        $activityFeed = $this->get('board.service.history')->getUserActivity($this->getUser(), $user);

        return array(
            'user' => $user,
            'feed' => $activityFeed,
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/profile", name="user_profile")
     * @Method("GET")
     * @Template("FlowerUserBundle:User:profile.html.twig")
     */
    public function profileAction()
    {
        $user = $this->getUser();
        $editForm = $this->createForm(new UserProfileType(), $user, array(
            'action' => $this->generateUrl('user_profile_update', array('id' => $user->getid())),
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
     * @Route("/profileUpdate", name="user_profile_update")
     * @Method("PUT")
     * @Template("FlowerUserBundle:User:profile.html.twig")
     */
    public function updateProfileAction(Request $request)
    {
        $user = $this->getUser();
        $editForm = $this->createForm(new UserProfileType(), $user, array(
            'action' => $this->generateUrl('user_profile_update', array('id' => $user->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');

            $user = $this->get('user.service.user')->uploadImage($user);

            $userManager->updateUser($user);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Perfil actualizado!');

            return $this->redirect($this->generateUrl('user_profile', array('id' => $user->getId())));
        }
        return array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/notifications", name="user_profile_notifications")
     * @Method("GET")
     * @Template("FlowerUserBundle:User:notifications.html.twig")
     */
    public function notificationsAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository("FlowerModelBundle:User\UserNotification")->findBy(array('user' => $user), array('created' => 'DESC'), 10, 0);
        return array(
            'notifications' => $notifications
        );
    }
}
