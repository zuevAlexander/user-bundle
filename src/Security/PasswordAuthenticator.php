<?php
namespace NorseDigital\UserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PasswordAuthenticator extends AbstractBaseAuthenticator
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * PasswordAuthenticator constructor.
     * @param TranslatorInterface $translator
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(TranslatorInterface $translator, UserPasswordEncoderInterface $encoder) {
        parent::__construct($translator);
        $this->encoder = $encoder;
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/login.json' || !$request->isMethod('POST')) {
            return null;
        }
        return [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
        ];
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];
        return $userProvider->loadUserByUsername($username);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $validPassword = $this->encoder->isPasswordValid(
            $user,
            $credentials['password']      // the submitted password
        );
        if (!$validPassword) {
            throw new CustomUserMessageAuthenticationException($this->failMessage);
        }
        return true;
    }
}