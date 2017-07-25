<?php

namespace AppBundle\Handler;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 11:21
 */
class LogoutHandler implements LogoutHandlerInterface
{
    /**
     * This method is called by the LogoutListener when a user has requested
     * to be logged out. Usually, you would unset session variables, or remove
     * cookies, etc.
     *
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     * @return Response
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $request->getSession()
            ->getFlashBag()
            ->add('info', 'You have been successfully disconnected');

        return $response;
    }
}