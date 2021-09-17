<?php
namespace Framework;

use Exception;
use Symfony\Component\HttpFoundation\{Session\Session,
            Response,
            Request};
use Framework\{Routes,
               Container,
               Logger,
               Handler,
               Exceptions\ContainerException,
               Exceptions\ContainerNotFoundException,
               Exceptions\RouteNotFoundException};
use function ob_end_clean;
use function ob_get_contents;
use function ob_get_length;

/**
 * This is the heart of system
 *
 *
 */
class Kernel extends Container {

    /**
     * @throws \InvalidArgumentException
     * @throws ContainerNotFoundException
     * @throws \Framework\Exceptions\ContainerException
     */
    public function run():void
    {
        app(Session::class)->start();
        //ob_start();
        $request = Request::createFromGlobals();
        $response = $this->handle($request);

        $response->send();
        //@ob_end_clean();
    }

    /**
     * Give response for every request
     *
     * @return Response
     *
     * @throws ContainerException
     * @throws ContainerNotFoundException
     */
    public function handle(Request $request) : Response
    {
        $response = $this->get(Handler::class)->handle($request);

        $response->prepare($request);

        return $response;
    }

    /**
     * @throws Exception
     */
    public function boot(string $basePath=null) : self
    {
        $this->set('logger', function () : Logger {
            return new Logger( app('basePath') . '/framework.log');
        });

        register_shutdown_function( "fatal_handler" );
        // Report simple running errors
        //error_reporting(E_ERROR | E_WARNING | E_PARSE);
        error_reporting(0);
        set_error_handler(function($errno, $errstr, $errfile, $errline){
            if ($errno === E_WARNING || E_USER_WARNING){
                // make it more serious than a warning so it can be caught
                //trigger_error($errstr, E_USER_ERROR);
                throw new \ErrorException($errstr, $errno, $errno, $errfile,  $errline);
                //return true;
            } else {
                // fallback to default php error handler
                return false;
            }
        });

        $this->setBasePath($basePath);
        $this->set(Request::class, Request::createFromGlobals());
        $this->collectRoutes();
        $this->set('version', '0.0.1');
        Routes::get('/version', function () : string {
            return app()->get('version');
        });

        return $this;
    }

    protected function setBasePath(string $basePath=null) : void
    {
        if ($basePath) {
            $this->set('basePath', $basePath);
        } else {
            $this->set('basePath', __DIR__);
        }
    }

    /**
     * @throws Exception
     */
    protected function collectRoutes() : void
    {
        $routePath = app('basePath') . '/config/routes.php';
        if (file_exists($routePath)) {
            require $routePath;
        } else {
            throw new Exception("Cannot load routes from $routePath");
        }
    }
}
