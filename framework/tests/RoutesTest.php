<?php

use Framework\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\Request;
use Framework\Routes;

class BarController {
    public function show(Request $request, int $id): string
    {
        return $request->getMethod() . " $id";
    }

    public function index(Request $request): string
    {
        return "index page";
    }

    /**
     * @return never
     */
    public function boom(Request $request)
    {
        throw new Exception;
    }
}
/**
 * Class RoutesTest
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class RoutesTest extends AbstractTest
{
    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Routes::get('/foo', function () { return 43; });
        Routes::get('/bar', BarController::class, 'index');
        Routes::post('/bar', BarController::class, 'create');
        Routes::put('/bar/{$id}', BarController::class, 'update');
        Routes::get('/bar/{$id}', BarController::class, 'show');
        Routes::delete('/bar/{$id}', BarController::class, 'remove');
        Routes::get('/boom', BarController::class, 'boom');
    }

    public function testRouteToClosure(): void
    {
        $this->assertEquals(
            [
                function () {
                    return 43;
                }
            ],
            Routes::find("GET", "/foo")
        );
        $this->get('/foo');
        $this->assertEquals("43", $this->response->getContent());
    }

    public function testRouteToController(): void
    {
        $this->assertEquals(
            [
                BarController::class,
                'index'
            ],
            Routes::find("GET", "/bar")
        );
        $this->get('/bar');
        $this->assertEquals("index page", $this->response->getContent());
    }

    public function testRouteWithSegment(): void
    {
        $this->assertEquals(
            [
                BarController::class,
                'show',
                'params' => [ "id" => 43 ]
            ],
            Routes::find("GET", "/bar/43")
        );
        $this->get('/bar/43');
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals("GET 43", $this->response->getContent());
    }

    public function testRoutePostRequest(): void
    {
        $this->assertEquals(
            [
                BarController::class,
                'create'
            ],
            Routes::find(Request::METHOD_POST, "/bar")
        );
    }

    public function testRoutePutRequest(): void
    {
        $this->assertEquals(
            [
                BarController::class,
                'update',
                'params' => [ 'id' => 34 ]
            ],
            Routes::find(Request::METHOD_PUT, "/bar/34")
        );
    }

    public function testRouteDeleteRequest(): void
    {
        $this->assertEquals(
            [
                BarController::class,
                'remove',
                'params' => [ 'id' => 34 ]
            ],
            Routes::find(Request::METHOD_DELETE, "/bar/34")
        );
    }

    public function testInternalServerErrorOnProduction() : void
    {
        $this->assertEquals(
            [
                BarController::class,
                'boom'
            ],
            Routes::find(Request::METHOD_GET, "/boom")
        );
        $this->get('/boom');
        $this->assertEquals(500, $this->response->getStatusCode());
        $this->assertEquals("Internal Server Error", $this->response->getContent());
    }

    public function testInternalServerErrorNonProduction() : void
    {
        putenv('APP_ENV=dev');
        $this->assertEquals(
            [
                BarController::class,
                'boom'
            ],
            Routes::find(Request::METHOD_GET, "/boom")
        );
        $this->get('/boom');
        $this->assertEquals(500, $this->response->getStatusCode());
        $this->assertStringContainsString("main", $this->response->getContent());
    }
}
