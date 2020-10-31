<?php

namespace Riddlestone\Brokkr\Users\Test\Unit;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Module;

class ModuleTest extends TestCase
{
    public function testGetConfig()
    {
        $module = new Module();

        $this->assertIsArray($module->getConfig());
    }
}
