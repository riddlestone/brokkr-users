<?php

namespace Riddlestone\Brokkr\Users\Entity;

use Exception;
use Laminas\Permissions\Acl\Role\RoleInterface;

class User implements RoleInterface
{
    // region Fields

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string|null
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var string
     */
    protected $passwordSalt;

    /**
     * @var string
     */
    protected $passwordHash;

    // endregion Fields

    // region Magic Methods

    public function __construct()
    {
        $this->setPasswordSalt(sha1(random_bytes(32)));
    }

    // endregion Magic Methods

    // region Getters and Setters

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return implode(' ', array_filter([$this->getFirstName(), $this->getLastName()])) ?: null;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    /**
     * @param string $passwordSalt
     */
    public function setPasswordSalt(string $passwordSalt): void
    {
        $this->passwordSalt = $passwordSalt;
    }

    /**
     * @return string
     */
    public function getPasswordSalt(): string
    {
        return $this->passwordSalt;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    // endregion Getters and Setters

    // region Password Methods

    /**
     * @param string $password
     * @param string $globalSalt
     * @return string
     */
    private function generatePasswordHash(string $password, string $globalSalt)
    {
        return sha1($password . $globalSalt . $this->getPasswordSalt());
    }

    /**
     * @param string $password
     * @param string $globalSalt
     * @return void
     */
    public function setPassword(string $password, string $globalSalt): void
    {
        $this->setPasswordHash($this->generatePasswordHash($password, $globalSalt));
    }

    /**
     * @param string $password
     * @param string $globalSalt
     * @return bool
     */
    public function checkPassword(string $password, string $globalSalt): bool
    {
        return $this->generatePasswordHash($password, $globalSalt) === $this->getPasswordHash();
    }

    // endregion Password methods

    // region RoleInterface

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getRoleId()
    {
        if (! $this->getId()) {
            throw new Exception('Cannot get role ID of User before they have an ID');
        }
        return __CLASS__ . ':' . $this->getId();
    }

    // endregion RoleInterface
}
