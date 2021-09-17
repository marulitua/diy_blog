<?php

namespace Framework;

use ValueError;
use TypeError;
use Throwable;
use Framework\Routes;
use Framework\ErrorExporter;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\HttpFoundation\{Request,
                                      Response};
use Framework\Exceptions\{ForbiddenException,
                         RouteNotFoundException,
                         BadConfigurationException};
use Framework\Reporter;
use function is_object;
use function sprintf;

/**
 * Class Handler
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class Handler
{
    /**
     * Give response for every request
     *
     * @throws \InvalidArgumentException
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     *
     * @psalm-suppress PossiblyUnusedMethod
     *
     * @return Response
     */
    public function handle(Request $request) : Response
    {
        try {
            $resultRoute = Routes::find($request->getMethod(), $request->getPathInfo());

            $targetClass = null;
            if (isset($resultRoute[0])) {
                $targetClass = $resultRoute[0];

                if (is_object($targetClass)) {
                    app()->set('response', $targetClass);
                    return new Response(app('response'), Response::HTTP_OK);
                }
            }

            if (isset($resultRoute[1])) {
                $targetMethod = $resultRoute[1];
                $controller = app($targetClass);
                $reflection = new \ReflectionMethod($controller, $targetMethod);
                $new_parameters = [];
                foreach ($reflection->getParameters() as $parameter) {
                    if (array_key_exists('params', $resultRoute) && array_key_exists($parameter->getName(), $resultRoute["params"])) {
                        $new_parameters[$parameter->getName()] = $resultRoute["params"][$parameter->getName()];

                    } else {
                        $new_parameters[$parameter->getName()] = app((string) $parameter->getType());
                    }
                }

                $response = call_user_func_array([$controller, $targetMethod], $new_parameters);
                if (is_string($response) || is_numeric($response)) {
                    $response = new Response((string) $response, Response::HTTP_OK);
                }
                return $response;
            }

            throw new BadConfigurationException(
                sprintf(
                    "Cannot resolve %s with method %s",
                    $request->getPathInfo(),
                    $request->getMethod()
                )
            );

        } catch (RouteNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (ForbiddenException $e) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        } catch (Throwable | \ErrorException $e) {
            $reporter = app(Reporter::class)->report($e);
            $error = app(ErrorExporter::class)->export($e);
            return new Response($error, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
