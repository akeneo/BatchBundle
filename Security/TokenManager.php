<?php

namespace Akeneo\Bundle\BatchBundle\Security;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenManager
{
    const DEFAULT_USERNAME = 'batch';

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var string
     */
    protected $userClass;

    /**
     * @var string
     */
    protected $userFieldIdentifier;

    /**
     * @var string
     */
    protected $adminIdentifier;

    /**
     * @param RegistryInterface $registry
     * @param string            $userClass
     * @param string            $userFieldIdentifier
     * @param string            $adminIdentifier
     */
    public function __construct(RegistryInterface $registry, $userClass, $userFieldIdentifier, $adminIdentifier = null)
    {
        $this->registry            = $registry;
        $this->userClass           = $userClass;
        $this->userFieldIdentifier = $userFieldIdentifier;
        $this->adminIdentifier     = $adminIdentifier;
    }

    public function get($identifier = null)
    {
        $identifier = (null === $identifier) ? $this->adminIdentifier : $identifier;
        $user       = (null === $identifier) ? null : $this->getUserFromIdentifier($identifier);

        return $this->create($user);
    }

    /**
     * @param UserInterface $user
     *
     * @return PreAuthenticatedToken
     */
    protected function create(UserInterface $user = null)
    {
        if (null === $user) {

            return new PreAuthenticatedToken(
                self::DEFAULT_USERNAME,
                uniqid('credentials_'),
                uniqid('providerKey_')
            );
        }

        return new PreAuthenticatedToken(
            $user->getUsername(),
            $user->getPassword(),
            uniqid('providerKey_'),
            $user->getRoles()
        );
    }

    /**
     * @param $identifier
     *
     * @return UserInterface
     */
    protected function getUserFromIdentifier($identifier)
    {
        return $this->registry->getRepository($this->userClass)
            ->findOneBy(array($this->userFieldIdentifier => $identifier));
    }
} 
