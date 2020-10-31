<?php

namespace Riddlestone\Brokkr\Users\Repository;

use Riddlestone\Brokkr\Users\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class User
 * @package Riddlestone\Brokkr\Users\Repository
 *
 * @method User findOneByEmailAddress(string $emailAddress)
 */
class UserRepository extends EntityRepository
{
    /**
     * @var string|null
     */
    protected $globalSalt;

    /**
     * @param string $globalSalt
     */
    public function setGlobalSalt(string $globalSalt): void
    {
        $this->globalSalt = $globalSalt;
    }

    /**
     * @return string|null
     */
    public function getGlobalSalt(): ?string
    {
        return $this->globalSalt;
    }

    /**
     * @param string $emailAddress
     * @param string $password
     * @return User|null
     */
    public function findOneByEmailAddressAndPassword(string $emailAddress, string $password)
    {
        $user = $this->findOneBy(['emailAddress' => $emailAddress]);
        if (
            !$user
            || !($user instanceof User)
            || !$user->checkPassword($password, $this->getGlobalSalt())
        ) {
            return null;
        }
        return $user;
    }
}
