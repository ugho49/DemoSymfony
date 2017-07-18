<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        /*$passwordEncoder = $this->get('security.password_encoder');

        $user = new User();
        $user
            ->setEmail("stephan.ugho@gmail.com")
            ->setFirstname("Ugho")
            ->setLastname("STEPHAN")
            ->addRole("ROLE_ADMIN");

        $user->setPassword($passwordEncoder->encodePassword($user, "test"));

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();*/

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }
}
