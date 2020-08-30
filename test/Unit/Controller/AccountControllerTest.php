<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
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

    /**
     * @var PluginManager|MockObject
     */
    private $pluginManager;

    /**
     * @var FlashMessenger|MockObject
     */
    private $flashMessenger;

    /**
     * @var Redirect|MockObject
     */
    private $redirect;

    protected function setUp(): void
    {
        $this->controller = new AccountController(
            $this->userRepository = $this->createMock(UserRepository::class),
            $this->authService = $this->createMock(AuthenticationService::class),
            $this->formElementManager = $this->createMock(AbstractPluginManager::class),
            $this->passwordResetService = $this->createMock(PasswordResetService::class)
        );
        $this->controller->setPluginManager(
            $this->pluginManager = $this->createMock(PluginManager::class)
        );
        $this->pluginManager
            ->method('has')
            ->willReturnCallback(function ($name) {
                return in_array($name, ['flashMessenger', 'redirect']);
            });
        $this->flashMessenger = $this->createMock(FlashMessenger::class);
        $this->redirect = $this->createMock(Redirect::class);
        $this->pluginManager
            ->method('get')
            ->willReturnMap([
                ['flashMessenger', null, $this->flashMessenger],
                ['redirect', null, $this->redirect],
            ]);
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

    public function testLogoutActionSuccess()
    {
        $this->authService
            ->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(true);
        $this->authService
            ->expects($this->once())
            ->method('clearIdentity');
        $this->flashMessenger
            ->expects($this->once())
            ->method('addSuccessMessage')
            ->with('Logout successful');
        $this->redirect
            ->expects($this->once())
            ->method('toRoute')
            ->with('home')
            ->willReturn($response = $this->createMock(Response::class));

        $this->assertEquals($response, $this->controller->logoutAction());
    }
}
