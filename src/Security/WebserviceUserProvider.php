<?php
namespace NorseDigital\UserBundle\Security;

use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityRepository;

class WebserviceUserProvider implements UserProviderInterface
{
    /**
     * @var EntityRepository
     */
    protected $userRepository;

    /**
     * WebserviceUserProvider constructor.
     * @param EntityRepository $userRepository
     */
    public function __construct(EntityRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $apiKey
     * @return null|object
     */
    public function getUserForApiKey($apiKey)
    {
        try {
            $userData = $this->userRepository->findOneBy(array('apiKey' => $apiKey));
        } catch (\Exception $e) {
            throw new TokenNotFoundException();
        }

        return $userData;
    }

    /**
     * @param string $username
     * @return null|object
     */
    public function loadUserByUsername($username)
    {
        try {
            $userData = $this->userRepository->findOneBy(array('username' => $username));
        } catch (\Exception $e) {
            throw new UsernameNotFoundException();
        }

        return $userData;
    }

    public function refreshUser(UserInterface $user)
    {
    }

    public function supportsClass($class)
    {
    }
}