<?php

namespace Framework\Tests;

use Throwable;
use PHPUnit\Framework\TestCase;
use Framework\Exceptions\ContainerException;

/**
* Class Framework\Tests\ContainerExceptionTest;
* @author Erwin Pakpahan <erwinmaruli@live.com>
* @psalm-suppress PropertyNotSetInConstructor
*/
final class ContainerExceptionTest extends TestCase
{
	public function testItImplementsIntefaces() : void
	{
		$exception = new ContainerException();
		$this->assertInstanceOf(ContainerException::class, $exception);
		$this->assertInstanceOf(Throwable::class, $exception);
	}
}
