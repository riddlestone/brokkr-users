<?php

namespace Riddlestone\Brokkr\Users\Test\Integration\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\ToolsException;
use Laminas\Http\Response;
use Laminas\View\Model\ViewModel;
use Riddlestone\Brokkr\Users\Controller\AccountController;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Form\LoginForm;
use Riddlestone\Brokkr\Users\Test\Integration\AbstractApplicationTestCase;

class AccountControllerTest extends AbstractApplicationTestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var AccountController
     */
    protected $controller;

    /**
     * @throws ToolsException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var EntityManager $em */
        $em = $this->app->getServiceManager()->get(EntityManager::class);
        $this->user = new User();
        $this->user->setFirstName('Test');
        $this->user->setLastName('User');
        $this->user->setEmailAddress('test@example.com');
        $this->user->setPassword('password', $this->app->getServiceManager()->get('Config')['global_salt']);
        $em->persist($this->user);
        $em->flush();
    }

    public function testIndexAction()
    {
        /** @var ViewModel $viewModel */
        $viewModel = $this->dispatch(AccountController::class, 'index');
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertEquals('brokkr/users/account/index', $viewModel->getTemplate());
        $this->assertNull($viewModel->getVariable('user', 'NOPE'));

        $this->authenticate($this->user);

        /** @var ViewModel $viewModel */
        $viewModel = $this->dispatch(AccountController::class, 'index');
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertEquals('brokkr/users/account/index', $viewModel->getTemplate());
        $this->assertEquals($this->user, $viewModel->getVariable('user', 'NOPE'));
    }

    public function testGetLoginAction()
    {
        /** @var ViewModel $viewModel */
        $viewModel = $this->dispatch(AccountController::class, 'login');
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertEquals('brokkr/users/account/login', $viewModel->getTemplate());
        $this->assertInstanceOf(LoginForm::class, $viewModel->getVariable('form'));
        $this->assertFalse($this->getAuthenticationService()->hasIdentity());
    }

    public function testPostValidLoginAction()
    {
        $this->app->getMvcEvent()->getRequest()->setMethod('POST');
        $post = $this->app->getMvcEvent()->getRequest()->getPost();
        $post->set('email_address', 'test@example.com');
        $post->set('password', 'password');

        $redirect = $this->dispatch(AccountController::class, 'login');
        $this->assertInstanceOf(Response::class, $redirect);
        $this->assertTrue($this->getAuthenticationService()->hasIdentity());
        $this->assertInstanceOf(User::class, $this->getAuthenticationService()->getIdentity());
        $this->assertEquals('test@example.com', $this->getAuthenticationService()->getIdentity()->getEmailAddress());
    }

    public function testPostInvalidFormLoginAction()
    {
        $this->app->getMvcEvent()->getRequest()->setMethod('POST');
        $post = $this->app->getMvcEvent()->getRequest()->getPost();
        $post->set('email_address', 'email-address-missing-at');
        $post->set('password', 'password');

        /** @var ViewModel $viewModel */
        $viewModel = $this->dispatch(AccountController::class, 'login');
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertEquals('brokkr/users/account/login', $viewModel->getTemplate());
        $this->assertInstanceOf(LoginForm::class, $viewModel->getVariable('form'));
        $this->assertFalse($this->getAuthenticationService()->hasIdentity());
    }

    public function testPostUserNotFoundLoginAction()
    {
        $this->app->getMvcEvent()->getRequest()->setMethod('POST');
        $post = $this->app->getMvcEvent()->getRequest()->getPost();
        $post->set('email_address', 'user-missing@example.com');
        $post->set('password', 'password');

        /** @var ViewModel $viewModel */
        $viewModel = $this->dispatch(AccountController::class, 'login');
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertEquals('brokkr/users/account/login', $viewModel->getTemplate());
        $this->assertInstanceOf(LoginForm::class, $viewModel->getVariable('form'));
        $this->assertFalse($this->getAuthenticationService()->hasIdentity());
    }

    public function testLogoutAction()
    {
        $this->authenticate($this->user);
        $this->assertTrue($this->getAuthenticationService()->hasIdentity());
        $this->assertInstanceOf(User::class, $this->getAuthenticationService()->getIdentity());
        $redirect = $this->dispatch(AccountController::class, 'logout');
        $this->assertInstanceOf(Response::class, $redirect);
        $this->assertFalse($this->getAuthenticationService()->hasIdentity());
    }
}
