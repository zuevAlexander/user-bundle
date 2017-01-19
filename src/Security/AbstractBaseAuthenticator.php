<?php

namespace NorseDigital\UserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractBaseAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * Default message for authentication failure.
     *
     * @var string
     */
    protected $failMessage = 'Invalid credentials';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TokenAuthenticator constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Called on every request. Return whatever credentials you want, or null to stop authentication.
     *
     * @param Request $request
     * @return mixed
     */
    abstract function getCredentials(Request $request);

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return mixed
     */
    abstract function getUser($credentials, UserProviderInterface $userProvider);

    /**
     * check credentials - e.g. make sure the password is valid
     *
     * @param mixed $credentials
     * @param UserInterface $user
     * @return mixed
     */
    abstract function checkCredentials($credentials, UserInterface $user);

    /**
     * On success, let the request continue
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * Called when invalid credentials
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );
        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            'message' => $this->translator->trans($this->failMessage)
        );
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}