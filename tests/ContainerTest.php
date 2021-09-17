<?php

namespace Blog\Tests;

use Psr\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Blog\Infrastructures\Container;
use DateTime;
use Blog\Exceptions\{ContainerException,
                     ContainerNotFoundException};

/**
 * Class ContainerTest
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class ContainerTest extends TestCase
{
    /**
     * The response
     *
     * @var Container
     */
    protected $container;

    protected function setUp() : void
    {
        $this->container = Container::getInstance();
    }

    public function testItImplementsContainerInterface()
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->container);
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
