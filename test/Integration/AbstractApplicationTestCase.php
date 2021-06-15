<?php

namespace Riddlestone\Brokkr\Users\Test\Integration;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;
use Laminas\Mvc\Application;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Entity\User;

abstract class AbstractApplicationTestCase extends TestCase
{
    /**
     * @var Application
     */
    protected $app;

    protected function getAppConfig(): array
    {
        return require __DIR__ . '/config/application.config.php';
    }

    /**
     * @throws ToolsException
     * @throws ORMException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app = Application::init($this->getAppConfig());
        /** @var EntityManager $em */
        $em = $this->app->getServiceManager()->get(EntityManager::class);
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $this->getAuthenticationService()->clearIdentity();
    }

    /**
     * @return AuthenticationService
     */
    protected function getAuthenticationService()
    {
        return $this->app->getServiceManager()->get(AuthenticationService::class);
    }

    protected function createAuthAdapter(User $user)
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('authenticate')->willReturn(new Result(Result::SUCCESS, $user));
        return $adapter;
    }

    protected function authenticate(User $user)
    {
        $this->getAuthenticationService()->authenticate($this->createAuthAdapter($user));
    }
}
