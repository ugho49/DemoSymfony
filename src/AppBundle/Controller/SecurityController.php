<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 18/07/2017
 * Time: 09:08
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserForgotPasswordType;
use AppBundle\Service\StringGenerator;
use DateInterval;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function loginAction()
    {
        // Get service
        $authUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // Forgot password form
        $forgotPasswordForm = $this->createForm(UserForgotPasswordType::class);

        return $this->render('security/login.html.twig', array(
            'last_email'            => $lastUsername,
            'error'                 => $error,
            "forgot_password_form"  => $forgotPasswordForm->createView()
        ));
    }

    /**
     * @Route("/forgot-password", name="forgot_password")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function forgotPasswordAction(Request $request)
    {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(User::class);

        $form = $this->createForm(UserForgotPasswordType::class);
        $form->handleRequest($request);

        $email = $form->get("email")->getData();
        $user = $repo->findByEmail($email);

        if ($user == null) {
            $form->get("email")->addError(new FormError("This email doesn't exist"));

            $request->getSession()
                ->getFlashBag()
                ->add('danger', "This email doesn't exist");
        }

        if ($form->isValid()) {
            $passwordEncoder = $this->get('security.password_encoder');
            $strGenerator = new StringGenerator();
            $newPasswordPlain = $strGenerator->generateRandomString(15);

            $dateValidUntil = new DateTime();
            $dateValidUntil->add(new DateInterval('PT2H'));
            // Password valid for 2 hours
            $user->setPasswordValidUntil($dateValidUntil);
            $user->setPassword($passwordEncoder->encodePassword($user, $newPasswordPlain));
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'A new password has been sent to you');

            $this->get("mail.service")->sendMail(
                "New Password", $user->getEmail(),
                $this->renderView("emails/forgot-password.html.twig", [
                    "password"      => $newPasswordPlain,
                    "valid_until"   => $dateValidUntil
                ])
            );
        }

        return $this->redirectToRoute('login');
    }
}