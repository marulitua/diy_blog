<?php

namespace HomeTest;

use App\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class HomeTest
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class HomeTest extends AbstractTest
{
    public function testHomeWithLogin() : void
    {
        app(Session::class)->set('username', 'foo');
        $this->get('/');
        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
        /** @psalm-suppress PossiblyFalseArgument */
        $this->assertStringContainsString("Hello foo", $this->response->getContent());
    }

    public function testHomeWithoutLogin() : void
    {
        $this->get('/');
        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
        /** @psalm-suppress PossiblyFalseArgument */
        $this->assertStringContainsString("Hello World", $this->response->getContent());
    }

}
