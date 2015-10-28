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
     * @Route("/avatar", name="user_avatar")
     * @Method("GET")
     * @Template("FlowerUserBundle:User:avatar.html.twig")
     */
    public function avatarAction()
    {
        $hash = md5(strtolower(trim($this->getUser()->getEmail())));
        return array(
            'hash' => $hash,
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
}
?>