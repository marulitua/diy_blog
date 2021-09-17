<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Framework\Traits\Secureable;
use function method_exists;

/**
 * Class ProfileController
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class ProfileController
{
    use Secureable;

    /**
     * @throws \Framework\Exceptions\ForbiddenException
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\RouteNotFoundException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    public function __construct()
    {
        if (method_exists($this, 'authorize')) {
            $this->authorize();
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function showProfile(Request $request) : Response
    {
        return new Response;
    }
}
