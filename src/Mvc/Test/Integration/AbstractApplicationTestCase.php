<?php

namespace Riddlestone\Brokkr\Users\Mvc\Test\Integration;

use Laminas\Mvc\Controller\PluginManager;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Router\Http\RouteMatch;
use Laminas\Uri\Http;
use Riddlestone\Brokkr\Users\Test\Integration\AbstractApplicationTestCase as UsersAbstractApplicationTestCase;

abstract class AbstractApplicationTestCase extends UsersAbstractApplicationTestCase
{
    protected function getAppConfig(): array
    {
        $parentConfig = parent::getAppConfig();
        return [
            'modules' => array_merge($parentConfig['modules'], [
                'Laminas\Form',
                'Laminas\Router',
                'Laminas\Mvc\Plugin\FlashMessenger',
                'Riddlestone\Brokkr\Users\Mvc',
            ]),
            'module_listener_options' => array_merge($parentConfig['module_listener_options'], [
                'config_glob_paths' => array_merge($parentConfig['module_listener_options']['config_glob_paths'], [
                    __DIR__ . '/config/local.config.php',
                ]),
            ]),
        ];
    }

    protected function dispatch(string $controllerName, string $actionName, array $params = [])
    {
        $this->app->getServiceManager()->get('Router')->setRequestUri(new Http('https://example.com'));
        $this->app->getMvcEvent()->setRouteMatch(
            new RouteMatch(array_merge(['controller' => $controllerName, 'action' => $actionName], $params))
        );
        $controller = $this->app->getServiceManager()
            ->get('ControllerManager')
            ->get($controllerName);
        $controller->setEvent($this->app->getMvcEvent());
        return $controller->dispatch(
            $this->app->getMvcEvent()->getRequest(),
            $this->app->getMvcEvent()->getResponse()
        );
    }

    /**
     * @param string $namespace
     * @return array
     */
    protected function getFlashMessages(string $namespace): array
    {
        /** @var PluginManager $pluginManager */
        $pluginManager = $this->app->getServiceManager()->get(PluginManager::class);
        /** @var FlashMessenger $flashMessenger */
        $flashMessenger = $pluginManager->get('flashMessenger');
        return $flashMessenger->getCurrentMessages($namespace);
    }
}
