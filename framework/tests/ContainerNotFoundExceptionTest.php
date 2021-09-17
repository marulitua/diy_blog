<?php

namespace Framework\Tests;

use Throwable;
use PHPUnit\Framework\TestCase;
use Framework\Exceptions\ContainerNotFoundException;

/**
* Class Framework\Tests\ContainerNotFoundExceptionTest;
* @author Erwin Pakpahan <erwinmaruli@live.com>
* @psalm-suppress PropertyNotSetInConstructor
*/
final class ContainerNotFoundExceptionTest extends TestCase
{
	public function testItImplementsInterfaces() : void
	{
		$exception = new ContainerNotFoundException();
		$this->assertInstanceOf(ContainerNotFoundException::class, $exception);
		$this->assertInstanceOf(Throwable::class, $exception);
	}
}
