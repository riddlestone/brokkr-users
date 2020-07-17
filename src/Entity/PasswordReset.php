<?php

namespace Riddlestone\Brokkr\Users\Entity;

use DateTimeImmutable;

class PasswordReset
{
    // region Fields

    /**
     * @var string
     */
    protected $id;

    /**
     * @var DateTimeImmutable
     */
    protected $validUntil;

    /**
     * @var User
     */
    protected $user;

    // endregion Fields

    // region Getter and Setters

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param DateTimeImmutable $validUntil
     */
    public function setValidUntil(DateTimeImmutable $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getValidUntil(): ?DateTimeImmutable
    {
        return $this->validUntil;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    // endregion Getters and Setters
}
