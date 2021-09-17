<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AuthController
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class AuthController
{
    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Framework\Exceptions\ForbiddenException
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    public function loginSubmit() : Response {
        $request = Request::createFromGlobals();
        $username = "root";
        $salt = "jJQX2MNmdXYmsW0hdjace5xwYMg3XOSc7oh8m0OF";
        $hash = "f971a4accc832d8df48154c287ff6017b82021d63e28f3143e5b5edf3cb3b7a8";

        $url = "/";
         // Used for login error messages

        sleep(3); // Makes it ages slower for rainbow attacks

        if (
            $request->request->has('un') &&
            $request->request->has('pw') &&
            $request->request->get('un') === $username &&
            hash("sha256", $request->request->get('pw').$salt) == $hash) {

            app(Session::class)->set('username', $request->request->get('un'));

            return new RedirectResponse($url);
        } else {
            dd(
                $request->request->all(),
                $request->request->get('un'),
                $username,
                hash("sha256", $request->request->get('pw').$salt, $hash)
            );
            $message = "Incorrect login data";
            return $this->renderLogin((string) $request->request->get('un'), $message);
        }
    }

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Framework\Exceptions\ForbiddenException
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    public function loginPage(Request $request) : Response
    {
        return $this->renderLogin((string) $request->request->get('un', ''));
    }

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Framework\Exceptions\ForbiddenException
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    protected function renderLogin(string $oldUserName, string $errorMessage=null) : Response
    {
        $data = [];
        $data['title'] = 'Login';
        $data['oldUserName'] = $oldUserName;
        $data['errorMessage'] = $errorMessage;

        return view(app('basePath') . '/src/views/login.php', $data);
    }

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Framework\Exceptions\ForbiddenException
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    public function logout(Request $request) : Response
    {
        /** @var Session */
        app(Session::class)->invalidate();
        return new RedirectResponse("/");
    }
}
