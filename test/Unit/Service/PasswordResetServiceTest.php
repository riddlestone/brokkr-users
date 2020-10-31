<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Router\RouteStackInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Mail\MessageFactory;
use Riddlestone\Brokkr\Users\Entity\PasswordReset;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\PasswordResetRepository;
use Riddlestone\Brokkr\Users\Service\PasswordResetService;

class PasswordResetServiceTest extends TestCase
{
    /**
     * @var MockObject|EntityManager
     */
    private $entityManager;

    /**
     * @var MockObject|PasswordResetRepository
     */
    private $repository;

    /**
     * @var MockObject|MessageFactory
     */
    private $messageFactory;

    /**
     * @var TransportInterface|MockObject
     */
    private $mailTransport;

    /**
     * @var RouteStackInterface|MockObject
     */
    private $router;

    /**
     * @var string
     */
    private $globalSalt;

    /**
     * @var PasswordResetService
     */
    private $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(PasswordResetRepository::class);
        $this->messageFactory = $this->createMock(MessageFactory::class);
        $this->mailTransport = $this->createMock(TransportInterface::class);
        $this->router = $this->createMock(RouteStackInterface::class);
        $this->globalSalt = 'GLOBAL_SALT_VALUE';

        $this->service = new PasswordResetService(
            $this->entityManager,
            $this->repository,
            $this->messageFactory,
            $this->mailTransport,
            $this->router,
            $this->globalSalt
        );
    }

    /**
     * @throws Exception
     */
    public function testCreateResetSuccess()
    {
        $user = new User();

        $this->entityManager
            ->expects($this->once())
            ->method('persist');
        $this->entityManager
            ->expects($this->once())
            ->method('flush');
        $this->router
            ->expects($this->once())
            ->method('assemble')
            ->willReturn('http://example.com/reset');
        $this->messageFactory
            ->expects($this->once())
            ->method('create')
            ->willReturnCallback(function ($htmlPath, $textPath, $params) use ($user) {
                $this->assertEquals('brokkr/users/mail/password-reset-request-html', $htmlPath);
                $this->assertEquals('brokkr/users/mail/password-reset-request-text', $textPath);
                $this->assertEquals($user, $params['user']);
                $this->assertEquals('http://example.com/reset', $params['resetLink']);
                $this->assertEquals('2 hours', $params['timeToLive']);
                return $this->createMock(Message::class);
            });
        $this->mailTransport
            ->expects($this->once())
            ->method('send');

        $this->service->createReset($user);
    }

    /**
     * @throws Exception
     */
    public function testCreateResetWithFailureToPersist()
    {
        $user = new User();

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willThrowException(new ORMException());
        $this->expectExceptionMessage('Could not save Password Reset information');

        $this->service->createReset($user);
    }

    /**
     * @throws Exception
     */
    public function testCreateResetWithFailureToFlush()
    {
        $user = new User();

        $this->entityManager
            ->expects($this->once())
            ->method('persist');
        $this->entityManager
            ->expects($this->once())
            ->method('flush')
            ->willThrowException($this->createMock(OptimisticLockException::class));
        $this->expectExceptionMessage('Could not save Password Reset information');

        $this->service->createReset($user);
    }

    /**
     * @throws Exception
     */
    public function testGetResetSuccess()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('+2 hours'));

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn($reset);

        $this->assertEquals($reset, $this->service->getReset($uuid));
    }

    /**
     * @throws Exception
     */
    public function testGetResetWithResetNotFound()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn(null);
        $this->expectExceptionMessage('Password reset request not found or has expired');

        $this->service->getReset($uuid);
    }

    /**
     * @throws Exception
     */
    public function testGetResetWithExpiredReset()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('-2 hours'));

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn($reset);
        $this->expectExceptionMessage('Password reset request not found or has expired');

        $this->service->getReset($uuid);
    }

    public function testValidateResetSuccess()
    {
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('+2 hours'));
        $this->assertTrue($this->service->validateReset($reset));
    }

    public function testValidateResetFailure()
    {
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('-2 hours'));
        $this->assertFalse($this->service->validateReset($reset));
    }

    /**
     * @throws Exception
     */
    public function testProcessResetSuccess()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';
        $oldPassword = 'old_password';
        $newPassword = 'new_password';
        $user = new User();
        $user->setPassword($oldPassword, $this->globalSalt);
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('+2 hours'));
        $reset->setUser($user);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn($reset);
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($reset);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');
        $this->router
            ->expects($this->once())
            ->method('assemble')
            ->willReturn('http://example.com/login');
        $this->messageFactory
            ->expects($this->once())
            ->method('create')
            ->willReturnCallback(function ($htmlPath, $textPath, $params) use ($user) {
                $this->assertEquals('brokkr/users/mail/password-reset-receipt-html', $htmlPath);
                $this->assertEquals('brokkr/users/mail/password-reset-receipt-text', $textPath);
                $this->assertEquals($user, $params['user']);
                $this->assertEquals('http://example.com/login', $params['loginLink']);
                return $this->createMock(Message::class);
            });
        $this->mailTransport
            ->expects($this->once())
            ->method('send');

        $this->service->processReset($uuid, $newPassword);

        $this->assertTrue($user->checkPassword($newPassword, $this->globalSalt));
    }

    /**
     * @throws Exception
     */
    public function testProcessResetWithResetNotFound()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';
        $oldPassword = 'old_password';
        $newPassword = 'new_password';
        $user = new User();
        $user->setPassword($oldPassword, $this->globalSalt);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn(null);
        $this->expectExceptionMessage('Password reset request not found or has expired');

        $this->service->processReset($uuid, $newPassword);

        $this->assertTrue($user->checkPassword($oldPassword, $this->globalSalt));
    }

    /**
     * @throws Exception
     */
    public function testProcessResetWithExpiredReset()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';
        $oldPassword = 'old_password';
        $newPassword = 'new_password';
        $user = new User();
        $user->setPassword($oldPassword, $this->globalSalt);
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('-2 hours'));
        $reset->setUser($user);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn($reset);
        $this->expectExceptionMessage('Password reset request not found or has expired');

        $this->service->processReset($uuid, $newPassword);

        $this->assertTrue($user->checkPassword($oldPassword, $this->globalSalt));
    }

    /**
     * @throws Exception
     */
    public function testProcessResetWithRemoveFailure()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';
        $oldPassword = 'old_password';
        $newPassword = 'new_password';
        $user = new User();
        $user->setPassword($oldPassword, $this->globalSalt);
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('+2 hours'));
        $reset->setUser($user);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn($reset);
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($reset)
            ->willThrowException(new ORMException());
        $this->expectExceptionMessage('Could not process password reset, please try again later');

        $this->service->processReset($uuid, $newPassword);
    }

    /**
     * @throws Exception
     */
    public function testProcessResetWithFlushFailure()
    {
        $uuid = 'cce98aaa-e925-11ea-95c5-02421fe097aa';
        $oldPassword = 'old_password';
        $newPassword = 'new_password';
        $user = new User();
        $user->setPassword($oldPassword, $this->globalSalt);
        $reset = new PasswordReset();
        $reset->setValidUntil(new DateTimeImmutable('+2 hours'));
        $reset->setUser($user);

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn($reset);
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($reset);
        $this->entityManager
            ->expects($this->once())
            ->method('flush')
            ->willThrowException($this->createMock(OptimisticLockException::class));
        $this->expectExceptionMessage('Could not process password reset, please try again later');

        $this->service->processReset($uuid, $newPassword);
    }
}
