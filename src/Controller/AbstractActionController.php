<?php

namespace Riddlestone\Brokkr\Users\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController as MvcAbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use Riddlestone\Brokkr\Acl\Acl;
use Riddlestone\Brokkr\Acl\Exception\ResourceNotFound;
use Riddlestone\Brokkr\Acl\Exception\RoleNotFound;

abstract class AbstractActionController extends MvcAbstractActionController
{
    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @param Acl $acl
     */
    public function setAcl(Acl $acl): void
    {
        $this->acl = $acl;
    }

    /**
     * @param AuthenticationService $authenticationService
     */
    public function setAuthenticationService(AuthenticationService $authenticationService): void
    {
        $this->authenticationService = $authenticationService;
    }

    public function forbiddenAction()
    {
        $this->getResponse()->setStatusCode(403);
        $viewModel = new ViewModel(['content' => 'Forbidden']);
        $viewModel->setTemplate('error/403');
        return $viewModel;
    }

    /**
     * Execute the request
     *
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $resource = static::class . '::' . static::getMethodFromAction($e->getRouteMatch()->getParam('action'));
        $role = $this->authenticationService->hasIdentity()
            ? $this->authenticationService->getIdentity()
            : null;

        try {
            if (!$this->acl->isAllowed($role, $resource)) {
                $e->getRouteMatch()->setParam('action', 'forbidden');
            }
        } catch (ResourceNotFound|RoleNotFound $exception) {
            // if role or resource is not found, allow access
        }

        return parent::onDispatch($e);
    }
}
