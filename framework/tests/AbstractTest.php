<?php

namespace Framework\Tests;

use Framework\Kernel;
use Framework\Routes;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AbstractTest extends TestCase
{
    /**
     * The response
     *
     * @var Response
     */
    protected $response;

    /**
     * The response
     *
     * @var \Framework\Kernel $app
     */
    protected $app;
    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->getMockBuilder(Kernel::class)
            ->onlyMethods(['collectRoutes'])
            ->getMock();

        $this->app->boot();
    }

    /**
     * Emulate a GET request
     *
     * @param string $uri
     *
     * @return void
     */
    protected function get(string $uri, array $content=[]) : void
    {
        $this->call(uri: $uri . '?' . http_build_query($content));
    }

    /**
     *  Do POST request on test
     *
     *  @param string $uri
     *  @param array  $payload
     *
     *  @return void
    */
    protected function post(string $uri, array $payload) : void
    {
        $this->call(method: Request::METHOD_POST, uri: $uri, payload: $payload);
    }

    protected function call(string $method=Request::METHOD_GET, string $uri="/", array $payload = []): void
    {
        $request = Request::create(
            $uri,
            $method,
            $payload
        );

        $this->response = $this->app->handle($request);
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        app(Session::class)->invalidate();
        Routes::reset();
        Kernel::reset();
    }
}
