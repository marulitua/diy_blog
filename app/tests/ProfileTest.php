<?php

use App\Tests\AbstractTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileTest
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ProfileTest extends AbstractTest
{
    public function testItWillAskUserToLoginFirst() : void
    {
        $this->get('/profile');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->response->getStatusCode());
    }
}
