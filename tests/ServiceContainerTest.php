<?php

/**
 * Tests the service container.
 */

namespace Itafroma\Zork\Tests;

use Itafroma\Zork\ServiceContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use \ReflectionClass;

class ServiceContainerTest extends ZorkTest
{
    /**
     * Tests Itafroma\Zork\ServiceContainer::getInstance().
     *
     * @covers Itafroma\Zork\ServiceContainer::getContainer
     */
    public function testGetContainer()
    {
        $container = ServiceContainer::getContainer();

        self::assertEquals(spl_object_hash($container), spl_object_hash(ServiceContainer::getContainer()));
    }

    /**
     * Tests Itafroma\Zork\ServiceContainer::getInstance() with reset.
     *
     * @covers Itafroma\Zork\ServiceContainer::getContainer
     */
    public function testGetContainerReset()
    {
        $container = ServiceContainer::getContainer();

        self::assertNotEquals(spl_object_hash($container), spl_object_hash(ServiceContainer::getContainer(true)));
    }

    /**
     * Tests Itafroma\Zork\ServiceContainer::create().
     *
     * @covers Itafroma\Zork\ServiceContainer::create
     */
    public function testCreate()
    {
        self::assertInstanceOf(ContainerBuilder::class, ServiceContainer::create());
    }
}
