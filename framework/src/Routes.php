<?php

namespace Framework;

use function addslashes;
use function array_filter;
use function array_key_exists;
use function explode;
use function in_array;
use function preg_match;
use function strpos;
use function strtolower;
use Closure;
use function strtoupper;
use Framework\Exceptions\RouteNotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Routes
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class Routes
{
    /** @var mixed $instance */
    protected static $instance;

    public static function reset(): void {
        self::$instance = null;
    }

    public static function get(string $uri, string|Closure $controller, string $action=null): void
    {
        self::push(Request::METHOD_GET , $uri, $controller, $action);
    }

    public static function post(string $uri, string|Closure $controller, string $action=null): void
    {
        self::push(Request::METHOD_POST, $uri, $controller, $action);
    }

    public static function delete(string $uri, string|Closure $controller, string $action=null): void
    {
        self::push(Request::METHOD_DELETE, $uri, $controller, $action);
    }

    public static function put(string $uri, string|Closure $controller, string $action=null): void
    {
        self::push(Request::METHOD_PUT, $uri, $controller, $action);
    }

    private static function push(string $method, string $uri, string|Closure $controller, string $action=null): void
    {
        self::$instance[$method][$uri] = [ $controller ];
        if ($action) {
            self::$instance[$method][$uri][] = $action;
        }
    }


    /**
     * undocumented function
     *
     * @throws RouteNotFoundException
     *
     * @return array
     */
    public static function find(string $method, string $uri) : array
    {
        $requestedMethod = strtoupper($method);
        $requestedUri = strtolower($uri);

        if (is_array(self::$instance) && array_key_exists($requestedMethod, self::$instance)) {
            foreach (self::$instance[$requestedMethod] as $registeredUri => $record) {
                $registeredUri = strtolower($registeredUri);

                if ($registeredUri === $requestedUri) {
                    return $record;
                }

                $re = '/\/[[:alnum:]]+\/\{\$(?\'segmentName\'[[:alnum:]]+)\}/';
                preg_match_all($re, $registeredUri, $registerdMatches, PREG_SET_ORDER, 0);

                $re = '/\/[[:alnum:]]+\/(?\'segmentId\'[[:alnum:]]+)/';
                preg_match_all($re, $requestedUri, $requestedMatches, PREG_SET_ORDER, 0);

                if (isset($registerdMatches[0]['segmentName']) && isset($requestedMatches[0]['segmentId'])) {
                    $record[ "params"] = [ $registerdMatches[0]['segmentName'] => $requestedMatches[0]['segmentId']];
                    return $record;
                }
            }
        }

        throw new RouteNotFoundException("Can't found route for $method $uri");
    }

}
