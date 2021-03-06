<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 10:58
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserChangePasswordType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 *
 * @Route("user-profile")
 */
class UserController extends Controller
{

    /**
     * Finds and displays a user entity.
     *
     * @Route("/", name="user_profile")
     * @Method("GET")
     * @return Response
     */
    public function showAction()
    {
        /** @var User $currentUser */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $deleteCommentForm = $this->createDeleteProfilePictureForm();

        return $this->render('user/show.html.twig', array(
            'user' => $user,
            "delete_comment_form" => $deleteCommentForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/edit", name="user_profile_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(User::class);
        $user = $repo->find($currentUser->getId());

        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('info', 'The user has been successfully updated');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/change-password", name="user_profile_change_password")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function changePasswordAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUserInSession = $this->get('security.token_storage')->getToken()->getUser();

        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(User::class);
        $currentUserInBase = $repo->find($currentUserInSession->getId());

        $passwordEncoder = $this->get('security.password_encoder');

        $form = $this->createForm(UserChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $oldPassword = $form->get("old_password")->getData();
            if (!$passwordEncoder->isPasswordValid($currentUserInBase, $oldPassword)) {
                $form->get('old_password')->addError(new FormError("The password is not right."));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $newPassword = $form->get("new_password")->getData();

            $currentUserInBase->setPasswordValidUntil(null);
            $currentUserInBase->setPassword($passwordEncoder->encodePassword($currentUserInBase, $newPassword));
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('info', 'The password has been successfully updated');

            // TODO : send email

            // $this->get("mail.service")->sendMail(
            //     "test", "test@test.fr",
            //     $this->renderView("emails/registration.html.twig")
            // );

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/change-password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Delete profile picture to an existing user entity.
     *
     * @Route("/delete-profile-picture", name="user_delete_profile_picture")
     * @Method({"DELETE"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteProfilePictureAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(User::class);
        $user = $repo->find($currentUser->getId());

        $em->remove($user->getFile());
        $em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('info', 'The picture has been successfully deleted');

        return $this->redirectToRoute('user_profile');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @return Form The form
     */
    private function createDeleteProfilePictureForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete_profile_picture'))
            ->setMethod('DELETE')
            ->getForm();
    }
}