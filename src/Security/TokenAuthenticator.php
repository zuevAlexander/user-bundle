<?php

namespace NorseDigital\UserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TokenAuthenticator extends AbstractBaseAuthenticator
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    private $authenticateTokenName;

    /**
     * TokenAuthenticator constructor.
     * @param TranslatorInterface $translator
     * @param string $authenticateTokenName
     */
    public function __construct(TranslatorInterface $translator, string $authenticateTokenName = 'battle')
    {
        parent::__construct($translator);
        $this->authenticateTokenName = $authenticateTokenName;
    }

    /**
     * Called on every request. Return whatever credentials you want, or null to stop authentication.
     * What you return will be passed to getUser() as $credentials
     * no token? Return null and no other methods will be called
     *
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request)
    {

        if (!$token = $request->headers->get($this->authenticateTokenName)) {
             return null;
        }
        return ['token' => $token];
    }

    /**
     *
     * if return null - authentication will fail, if a User object - checkCredentials() is called
     *
     * @param mixed $credentials
     * @param UserProviderInterface|WebserviceUserProvider $userProvider
     * @return mixed
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $credentials['token'];
        return $userProvider->getUserForApiKey($apiKey);
    }

    /**
     * no credential check is needed in this case
     *
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
}