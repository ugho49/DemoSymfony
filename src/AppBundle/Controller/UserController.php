<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 10:58
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 */
class UserController extends Controller
{

    /**
     * Finds and displays a user entity.
     *
     * @Route("/user", name="user_profile")
     * @Method("GET")
     * @return Response
     */
    public function showAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('user/show.html.twig', array(
            'user' => $user,
        ));
    }

}