<?php

use Framework\Kernel;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\AbstractTest;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AppTest extends AbstractTest
{
    public function testCanResponseToRootRequest(): void
    {
        $this->get('/');
        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
        /** @psalm-suppress PossiblyFalseArgument */
        $this->assertStringContainsString("Hello World", $this->response->getContent());
    }

    public function testCanGetTheRightRoute(): void
    {
        $this->get('/foo');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->response->getStatusCode());
    }
}
