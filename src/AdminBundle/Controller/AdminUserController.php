<?php

namespace AdminBundle\Controller;

use AdminBundle\Enum\RolesEnum;
use AdminBundle\Form\AdminUserType;
use AdminBundle\Form\EditUserType;
use AdminBundle\Form\NewUserType;
use AppBundle\Entity\User;
use AppBundle\Service\MailService;
use AppBundle\Service\StringGenerator;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin User controller.
 *
 * @Route("user")
 */
class AdminUserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="admin_user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="admin_user_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user, array(
            "current_user" => $currentUser
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $strGenerator = new StringGenerator();
            $plainPassword = $strGenerator->generateRandomString(15);
            $passwordEncoder = $this->get('security.password_encoder');
            $user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));

            $dateValidUntil = new DateTime();
            $dateValidUntil->add(new DateInterval('P7D'));
            // Password valid for 7 days
            $user->setPasswordValidUntil($dateValidUntil);

            $em->persist($user);
            $em->flush();

            $this->get("mail.service")->sendMail(
                "Welcome", $user->getEmail(),
                $this->renderView("emails/new-user.html.twig", [
                    "user"          => $user,
                    "password"      => $plainPassword,
                    "valid_until"   => $dateValidUntil
                ])
            );

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'The user has been successfully created');

            return $this->redirectToRoute('admin_user_show', array('id' => $user->getId()));
        }

        return $this->render('admin/user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="admin_user_show")
     * @Method("GET")
     * @param User $user
     * @return Response
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createEnableDisableForm($user);

        return $this->render('admin/user/show.html.twig', array(
            'user' => $user,
            'enable_disable_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param User $user
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        // Can't edit a superadmin or edit yourselft
        if ($user->hasRole(RolesEnum::ROLE_SUPERADMIN) || $user->getId() == $currentUser->getId()) {
            throw $this->createAccessDeniedException();
        }

        $editForm = $this->createForm(AdminUserType::class, $user, array(
            "current_user" => $currentUser
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('info', 'The user has been successfully updated');

            return $this->redirectToRoute('admin_user_show', array('id' => $user->getId()));
        }

        return $this->render('admin/user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}/enable-disable", name="admin_user_enable_disable")
     * @Method("PUT")
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function enableDisableAction(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        // Can't update a superadmin or update yourselft
        if ($user->hasRole(RolesEnum::ROLE_SUPERADMIN) || $user->getId() == $currentUser->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEnableDisableForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user->setEnabled(!$user->isEnabled());

            $em->flush();

            $text = $user->isEnabled() ? "enabled" : "disabled";

            $request->getSession()
                ->getFlashBag()
                ->add('info', 'The user has been successfully ' . $text);
        }

        return $this->redirectToRoute('admin_user_show', array('id' => $user->getId()));
    }

    /**
     * Creates a form to Enable Or Disable a user entity.
     *
     * @param User $user The user entity
     *
     * @return Form The form
     */
    private function createEnableDisableForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_enable_disable', array('id' => $user->getId())))
            ->setMethod('PUT')
            ->getForm()
        ;
    }
}
