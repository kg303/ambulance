<?php

// src/Security/ApiKeyAuthenticator.php
namespace App\Security;

use App\Model\DataObject\Doctor;
use App\Form\LoginFormType;
use Pimcore\Security\User\ObjectUserProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Form\FormFactoryInterface;

class CustomAuthenticator extends AbstractAuthenticator
{

    private ObjectUserProvider $objectUserProvider;

    public function __construct(
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        ObjectUserProvider $objectUserProvider
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->objectUserProvider = $objectUserProvider;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        $loginForm = $this->formFactory->create(LoginFormType::class);
        $loginForm->handleRequest($request);

        return $loginForm->isSubmitted() && $loginForm->isValid();
    }

    public function authenticate(Request $request): Passport
    {
        $loginForm = $this->formFactory->create(LoginFormType::class);
        $loginForm->handleRequest($request);
        $credentials = $loginForm->getData();
//        try {
            $user = $this->objectUserProvider->loadUserByIdentifier($credentials['_username']);
            $userIdentifier = $user->getUsername();
//        } catch (\Exception $e) {
//            $userIdentifier = $credentials['ERROR'];
//        }

        return new SelfValidatingPassport(new UserBadge($userIdentifier));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $url = $this->urlGenerator->generate('home');

        return new RedirectResponse($url);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $url = $this->urlGenerator->generate('login', ['loginFailed' => true]);

        return new RedirectResponse($url);
    }
}
