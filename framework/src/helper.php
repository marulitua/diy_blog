<?php

use Framework\Container;
use Framework\Kernel;
use Framework\View;
use Symfony\Component\HttpFoundation\Response;
use Framework\ErrorExporter;
use Framework\Reporter;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @throws Framework\Exceptions\ContainerException
     * @throws Framework\Exceptions\ContainerNotFoundException
     *
     * @param  string  $make
     * @return mixed
     */
    function app($make = null)
    {
        $app = Kernel::getInstance();
        if (is_null($make)) {
            return $app;
        }

        return $app->get($make);
    }
}

if (! function_exists('view')) {
    /**
     * Get the available container instance.
     *
     * @throws \RuntimeException
     * @throws InvalidArgumentException
     * @throws Framework\Exceptions\ForbiddenException
     * @throws Framework\Exceptions\ContainerException
     * @throws Framework\Exceptions\ContainerNotFoundException
     *
     * @param  string  $template
     * @param  array   $data
     * @param  View    $engine
     *
     * @return Response
     */
    function view(string $template, array $data, View $engine=null)
    {
        /* @var \Framework\View $engine */
        /*
        $engine = app(View::class);

         */
        if (is_null($engine)) {
            $engine = app(View::class);
        }
        $engine->data = $data;

        $content = $engine->render($template);
        return new Response($content, Response::HTTP_OK);
    }
}

        function fatal_handler() {
            $errfile = "unknown file";
            $errstr  = "shutdown";
            $errno   = E_CORE_ERROR;
            $errline = 0;

            $error = error_get_last();

            if ($error !== null) {
                $errno   = $error["type"];
                $errfile = $error["file"];
                $errline = $error["line"];
                $errstr  = $error["message"];

                $exception = new \ErrorException($errstr, $errno, $errno, $errfile,  $errline);
                $error = app(ErrorExporter::class)->export($exception);
                app(Reporter::class)->report($exception);

                if (php_sapi_name() !== "cli") {
                    $response = new Response($error, Response::HTTP_INTERNAL_SERVER_ERROR);
                    $response->send();
                }
            }
        }


   function isProduction() : bool {
        return env('APP_ENV', true) === true;
    }
