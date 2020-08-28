<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\View\Model\ViewModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Controller\AccountController;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\UserRepository;
use Riddlestone\Brokkr\Users\Service\PasswordResetService;

class AccountControllerTest extends TestCase
{
    /**
     * @var MockObject|UserRepository
     */
    private $userRepository;

    /**
     * @var AuthenticationService|MockObject
     */
    private $authService;

    /**
     * @var AbstractPluginManager|MockObject
     */
    private $formElementManager;

    /**
     * @var MockObject|PasswordResetService
     */
    private $passwordResetService;

    /**
     * @var AccountController
     */
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new AccountController(
            $this->userRepository = $this->createMock(UserRepository::class),
            $this->authService = $this->createMock(AuthenticationService::class),
            $this->formElementManager = $this->createMock(AbstractPluginManager::class),
            $this->passwordResetService = $this->createMock(PasswordResetService::class)
        );
        $this->controller->setPluginManager($this->createMock(PluginManager::class));
    }

    public function testIndexAction()
    {
        $user = new User();

        $this->authService
            ->expects($this->once())
            ->method('getIdentity')
            ->willReturn($user);

        $view = $this->controller->indexAction();
        $this->assertInstanceOf(ViewModel::class, $view);
        $this->assertEquals($user, $view->getVariable('user'));
        $this->assertEquals('brokkr/users/account/index', $view->getTemplate());
    }
}
