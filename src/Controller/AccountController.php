<?php

namespace Riddlestone\Brokkr\Users\Controller;

use Exception;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\View\Model\ViewModel;
use Riddlestone\Brokkr\Users\Authentication\AuthenticationAdapter;
use Riddlestone\Brokkr\Users\Form\LoginForm;
use Riddlestone\Brokkr\Users\Form\PasswordResetForm;
use Riddlestone\Brokkr\Users\Form\RequestPasswordResetForm;
use Riddlestone\Brokkr\Users\Repository\UserRepository;
use Riddlestone\Brokkr\Users\Service\PasswordResetService;

/**
 * Class AccountController
 *
 * @package Riddlestone\Brokkr\Users
 * @method FlashMessenger flashMessenger()
 */
class AccountController extends AbstractActionController
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var AbstractPluginManager
     */
    protected $formElementManager;

    /**
     * @var PasswordResetService
     */
    protected $passwordResetService;

    public function __construct(
        UserRepository $userRepository,
        AuthenticationService $authService,
        AbstractPluginManager $formElementManager,
        PasswordResetService $passwordResetService
    ) {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->formElementManager = $formElementManager;
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $viewModel = new ViewModel(
            [
                'user' => $this->authService->getIdentity(),
            ]
        );
        $viewModel->setTemplate('brokkr/users/account/index');
        return $viewModel;
    }

    /**
     * @return Response
     */
    public function logoutAction()
    {
        if ($this->authService->hasIdentity() && $this->plugins->has('flashMessenger')) {
            $this->flashMessenger()->addSuccessMessage('Logout successful');
        }
        $this->authService->clearIdentity();
        return $this->redirect()->toRoute('home');
    }

    /**
     * @return Response|ViewModel
     */
    public function loginAction()
    {
        /** @var LoginForm $form */
        $form = $this->formElementManager->get(LoginForm::class);
        $viewModel = new ViewModel(['form' => $form]);
        $viewModel->setTemplate('brokkr/users/account/login');
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if (!$form->isValid()) {
                return $viewModel;
            }
            $data = $form->getData();
            $this->authService->authenticate(
                new AuthenticationAdapter($this->userRepository, $data['email_address'], $data['password'])
            );
            if ($this->authService->hasIdentity()) {
                if ($this->plugins->has('flashMessenger')) {
                    $this->flashMessenger()->addSuccessMessage('Login successful');
                }
                return $this->redirect()->toRoute('home');
            }
        }
        return $viewModel;
    }

    /**
     * @return Response|ViewModel
     * @throws Exception
     */
    public function requestPasswordResetAction()
    {
        /** @var RequestPasswordResetForm $form */
        $form = $this->formElementManager->get(RequestPasswordResetForm::class);
        $viewModel = new ViewModel(['form' => $form]);
        $viewModel->setTemplate('brokkr/users/account/request_password_reset');
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if (!$form->isValid()) {
                return $viewModel;
            }
            $data = $form->getData();
            $user = $this->userRepository->findOneByEmailAddress($data['email_address']);
            if ($user) {
                $this->passwordResetService->createReset($user);
            }
            if ($this->plugins->has('flashMessenger')) {
                $this->flashMessenger()->addSuccessMessage('Password reset sent');
            }
            return $this->redirect()->toRoute('home');
        }
        return $viewModel;
    }

    /**
     * @return Response|ViewModel
     * @throws Exception
     */
    public function resetPasswordAction()
    {
        $reset = $this->passwordResetService->getReset($this->params('id'));
        /** @var PasswordResetForm $form */
        $form = $this->formElementManager->get(
            PasswordResetForm::class,
            [
                'email_address' => $reset->getUser()->getEmailAddress(),
            ]
        );
        $viewModel = new ViewModel(['form' => $form]);
        $viewModel->setTemplate('brokkr/users/account/password_reset');
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if (!$form->isValid()) {
                return $viewModel;
            }
            $data = $form->getData();
            try {
                $this->passwordResetService->processReset($this->params('id'), $data['password']);
            } catch (Exception $e) {
                $form->setMessages(['error' => $e->getMessage()]);
                return $viewModel;
            }
            if ($this->plugins->has('flashMessenger')) {
                $this->flashMessenger()->addSuccessMessage('Password reset, please login');
            }
            return $this->redirect()->toRoute('brokkr-users/account/login');
        }
        return $viewModel;
    }
}
