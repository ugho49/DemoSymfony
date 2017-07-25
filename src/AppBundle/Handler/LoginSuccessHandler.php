<?php

namespace AppBundle\Handler;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 19/07/2017
 * Time: 11:21
 */
class LoginSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AuthenticationSuccessHandler constructor.
     * @param HttpUtils $httpUtils
     * @param EntityManagerInterface $em
     */
    function __construct(HttpUtils $httpUtils, EntityManagerInterface $em)
    {
        parent::__construct($httpUtils);
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $response = parent::onAuthenticationSuccess($request, $token);

        /** @var User $user */
        $user = $token->getUser();
        $user->setLastLogin(new \DateTime());
        $this->em->flush();

        $request->getSession()
            ->getFlashBag()
            ->add('info', 'Welcome ' . $user->getFullname());

        return $response;
    }
}