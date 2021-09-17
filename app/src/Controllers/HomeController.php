<?php

namespace App\Controllers;

use Framework\View;
use Framework\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Exception;

/**
* Class App\Controllers\HomeController;
* @author Erwin Pakpahan <erwinmaruli@live.com>
*/
class HomeController
{

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Framework\Exceptions\ForbiddenException
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    public function index (Request $request): Response {
        return view(
            app('basePath') . '/src/views/home.php',
            [
                'title' => 'Home',
                'errorMessage' => [],
                'username' => app(Session::class)->get('username')
            ]
        );
    }
}
