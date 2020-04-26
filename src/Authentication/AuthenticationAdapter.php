<?php

namespace Riddlestone\Brokkr\Users\Authentication;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class AuthenticationAdapter implements AdapterInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var string
     */
    protected $password;

    public function __construct(
        UserRepository $userRepository,
        $emailAddress,
        $password
    ) {
        $this->userRepository = $userRepository;
        $this->emailAddress = $emailAddress;
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function authenticate()
    {
        $user = $this->userRepository->findByEmailAddressAndPassword($this->emailAddress, $this->password);
        return new Result(
            $user ? Result::SUCCESS : Result::FAILURE,
            $user
        );
    }
}
