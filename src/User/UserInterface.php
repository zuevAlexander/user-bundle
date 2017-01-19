<?php


namespace NorseDigital\UserBundle\User;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    public function getApiKey();
}
