<?php

namespace Riddlestone\Brokkr\Users;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Helper\Navigation;
use Laminas\View\Renderer\PhpRenderer;
use Riddlestone\Brokkr\Acl\Acl;

class Module
{
    public function getConfig()
    {
        return require __DIR__ . '/../config/module.config.php';
    }

    /**
     * Register {@link onRender} with the application event manager
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $event->getApplication()->getEventManager()->attach('render', [$this, 'onRender']);
    }

    /**
     * Inject the current authenticated user into {@link Navigation} when rendering
     *
     * @param MvcEvent $event
     */
    public function onRender(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        if (
            !$serviceManager->has('ViewRenderer')
            || !($renderer = $serviceManager->get('ViewRenderer')) instanceof PhpRenderer
            || !$renderer->getHelperPluginManager()->has('navigation')
            || !($navigation = $renderer->getHelperPluginManager()->get('navigation')) instanceof Navigation
        ) {
            return;
        }

        /** @var Navigation $navigation */
        $navigation
            ->setAcl($serviceManager->get(Acl::class))
            ->setRole($serviceManager->get(AuthenticationService::class)->getIdentity());
    }
}
