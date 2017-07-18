<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 18/07/2017
 * Time: 09:08
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @return Response
     * @internal param Request $request
     */
    public function loginAction()
    {
        // Get service
        $authUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_email'    => $lastUsername,
            'error'         => $error,
        ));
    }
}