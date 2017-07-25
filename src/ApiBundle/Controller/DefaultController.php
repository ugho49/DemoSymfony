<?php

namespace ApiBundle\Controller;

use ApiBundle\Traits\RestControler;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    use RestControler;

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->jsonResponse($user);
    }
}
