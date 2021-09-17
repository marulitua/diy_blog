<?php declare(strict_types=1);

namespace Framework\Tests;

use Exception;
use Framework\Kernel;
use Framework\Handler;
use Framework\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class KernelTest
 * @psalm-suppress PropertyNotSetInConstructor
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
final class KernelTest extends AbstractTest
{
    public function testVersion(): void
    {
        $this->get('/version');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals("0.0.1", $this->response->getContent());
    }

    public function testItWillThrowExceptionIfCannotLoadRoutes() : void
    {
        $this->expectException(Exception::class);
        $newApp = new Kernel;
        $newApp->boot();
    }

    public function testItCanBeRun() : void
    {
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
             ->method('send');

        /* create new hander that will return mock response */
        $newHandler = new class() extends Handler
        {
            /** @var Response $response */
            public static $response;

            public function handle(Request $request) : Response
            {
                return static::$response;
            }
        };

        $newHandler::$response = $mockResponse;

        $newApp = new Kernel;
        $newApp->boot(__DIR__ );
        $newApp->set(Handler::class, $newHandler);
        $newApp->run();

        $this->assertIsString(app(Session::class)->getId());
    }
}
