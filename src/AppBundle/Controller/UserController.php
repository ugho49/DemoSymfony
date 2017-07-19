<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 10:58
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 */
class UserController extends Controller
{

    /**
     * Finds and displays a user entity.
     *
     * @Route("/user-profile", name="user_profile")
     * @Method("GET")
     * @return Response
     */
    public function showAction()
    {
        /** @var User $currentUser */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('user/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/user-profile/edit", name="user_profile_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("AppBundle:User");
        $user = $repo->find($currentUser->getId());

        $editForm = $this->createForm(new UserType(), $user);
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
}