<?php

namespace Riddlestone\Brokkr\Users;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Helper\Navigation;
use Laminas\View\Renderer\PhpRenderer;

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

        $renderer = $serviceManager->get('ViewRenderer');
        if (!$renderer instanceof PhpRenderer) {
            trigger_error('"ViewRenderer" not an instance of ' . PhpRenderer::class, E_USER_NOTICE);
            return;
        }

        $navigation = $renderer->getHelperPluginManager()->get('navigation');
        if (!$navigation instanceof Navigation) {
            trigger_error('"navigation" not an instance of ' . Navigation::class, E_USER_NOTICE);
            return;
        }

        $navigation->setRole($serviceManager->get(AuthenticationService::class)->getIdentity());
    }
}
