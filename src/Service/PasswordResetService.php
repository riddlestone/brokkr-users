<?php

namespace Riddlestone\Brokkr\Users\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Router\RouteInterface;
use Riddlestone\Brokkr\Mail\MessageFactory;
use Riddlestone\Brokkr\Users\Entity\PasswordReset;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\PasswordResetRepository;

class PasswordResetService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var PasswordResetRepository
     */
    protected $passwordResetsRepo;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var TransportInterface
     */
    protected $mailTransport;

    /**
     * @var RouteInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $globalSalt;

    public function __construct(
        EntityManager $entityManager,
        PasswordResetRepository $passwordResetsRepo,
        MessageFactory $messageFactory,
        TransportInterface $mailTransport,
        RouteInterface $router,
        string $globalSalt
    ) {
        $this->entityManager = $entityManager;
        $this->passwordResetsRepo = $passwordResetsRepo;
        $this->messageFactory = $messageFactory;
        $this->mailTransport = $mailTransport;
        $this->router = $router;
        $this->globalSalt = $globalSalt;
    }

    /**
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function createReset(User $user): void
    {
        $passwordReset = new PasswordReset();
        $passwordReset->setUser($user);
        $passwordReset->setValidUntil(new DateTimeImmutable('+2 hours'));
        try {
            $this->entityManager->persist($passwordReset);
            $this->entityManager->flush();
        } catch (ORMException | OptimisticLockException $e) {
            throw new Exception('Could not save Password Reset information', null, $e);
        }

        $mail = $this->messageFactory->create(
            'brokkr/users/mail/password-reset-request-html',
            'brokkr/users/mail/password-reset-request-text',
            [
                'user' => $user,
                'resetLink' => $this->router->assemble(
                    ['id' => $passwordReset->getId()],
                    ['name' => 'brokkr-users/account/reset-password', 'force_canonical' => true]
                ),
                'timeToLive' => '2 hours',
            ]
        );
        $mail->setTo($user->getEmailAddress(), $user->getName());
        $this->mailTransport->send($mail);
    }

    /**
     * @param string $id
     * @return PasswordReset|null
     * @throws Exception
     */
    public function getReset(string $id): PasswordReset
    {
        $reset = $this->passwordResetsRepo->find($id);
        if (!$reset || !$this->validateReset($reset)) {
            throw new Exception('Password reset request not found or has expired');
        }
        return $reset;
    }

    /**
     * Validates that the reset request exists, and is still valid
     *
     * @param PasswordReset $reset
     * @return bool
     */
    public function validateReset(PasswordReset $reset): bool
    {
        return $reset->getValidUntil() >= new DateTimeImmutable();
    }

    /**
     * Attempts to reset the user's password
     *
     * @param string $id
     * @param string $password
     * @throws Exception
     */
    public function processReset(string $id, string $password): void
    {
        $reset = $this->passwordResetsRepo->find($id);
        if (!$reset || !$this->validateReset($reset)) {
            throw new Exception('Password reset request not found or has expired');
        }
        $reset->getUser()->setPassword($password, $this->globalSalt);
        try {
            $this->entityManager->remove($reset);
            $this->entityManager->flush();
        } catch (ORMException | OptimisticLockException $e) {
            throw new Exception('Could not process password reset, please try again later', null, $e);
        }

        $mail = $this->messageFactory->create(
            'brokkr/users/mail/password-reset-receipt-html',
            'brokkr/users/mail/password-reset-receipt-text',
            [
                'user' => $reset->getUser(),
                'loginLink' => $this->router->assemble(
                    [],
                    ['name' => 'brokkr-users/account/login', 'force_canonical' => true]
                ),
            ]
        );
        $this->mailTransport->send($mail);
    }
}
