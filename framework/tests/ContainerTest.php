<?php

namespace Framework\Tests;

use DateTime;
use Framework\Container;
use Framework\Tests\AbstractTest;
use Psr\Container\ContainerInterface;
use Framework\Exceptions\ContainerNotFoundException;
use function get_class;

/**
 * Class Framework\Tests\ContainerTest
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @property Framework\Container container
 */
class ContainerTest extends AbstractTest
{
    /** @var \Framework\Container $container */
    protected $container;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        $this->container = new Container;
    }

    public function testItImplementsInterfaces(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->container);
    }

    public function testItHasSimpleClasses(): void
    {
        $this->assertFalse($this->container->has('FooBar'));
        $this->assertTrue($this->container->has(DateTime::class));
        $this->assertInstanceOf(DateTime::class, $this->container->get(DateTime::class));
    }

    public function testItReturnNotFoundExceptionIfClassCannotBeFound(): void
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->container->get('FooBar');
    }

    public function testItCanRegisterAndResolve(): void
    {
        $toResolve = new class {};
        $this->assertInstanceOf(Container::class, $this->container->set('Foo\Bar', $toResolve));
        $this->assertTrue($this->container->has('Foo\Bar'));
        $this->assertInstanceOf($toResolve::class, $this->container->get('Foo\Bar'));
    }

    public function testItCanRegisterAndResolveScalar(): void
    {
        $this->assertInstanceOf(Container::class, $this->container->set('Foo\Bar', 43));
        $this->assertTrue($this->container->has('Foo\Bar'));
        $this->assertEquals(43, $this->container->get('Foo\Bar'));
    }

    public function testItCanRegisterAndResolveArray(): void
    {
        $this->assertInstanceOf(Container::class, $this->container->set('Foo\Bar', [1, 2, 3]));
        $this->assertTrue($this->container->has('Foo\Bar'));
        $this->assertEquals([1, 2, 3], $this->container->get('Foo\Bar'));
    }

    public function testItCanResolveRegisteredCallable(): void
    {
        $toResolve = function (): \DateTime {
            return new DateTime;
        };
        $this->container->set('Foo\Bar', $toResolve);
        $this->assertInstanceOf(DateTime::class, $this->container->get('Foo\Bar'));
    }

    public function testItCanResolveDependencies(): void
    {
        $toResolve = get_class(new class(new DateTime) {
            public $datetime;
            public function __construct(DateTime $datetime)
            {
                $this->datetime = $datetime;
            }
        });
        $this->container->set('Foo\Bar', $toResolve);
        $this->assertTrue($this->container->has('Foo\Bar'));
        $this->assertInstanceOf($toResolve, $this->container->get('Foo\Bar'));
    }

}
