<?php

namespace Riddlestone\Brokkr\Users\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Riddlestone\Brokkr\Users\Authentication\AuthenticationAdapter;
use Riddlestone\Brokkr\Users\Form\LoginForm;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

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

    public function __construct(
        UserRepository $userRepository,
        AuthenticationService $authService,
        AbstractPluginManager $formElementManager
    ) {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->formElementManager = $formElementManager;
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
        $viewModel = new ViewModel(
            [
                'form' => $form,
            ]
        );
        $viewModel->setTemplate('brokkr/users/account/login');
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if (! $form->isValid()) {
                return $viewModel;
            }
            $data = $form->getData();
            $this->authService->authenticate(
                new AuthenticationAdapter($this->userRepository, $data['email_address'], $data['password'])
            );
            if ($this->authService->hasIdentity()) {
                return $this->redirect()->toRoute('home');
            }
        }
        return $viewModel;
    }
}
