<?php

namespace Framework;

use Closure;
use Psr\Container\ContainerInterface;
use Framework\Exceptions\ContainerException;
use Framework\Exceptions\ContainerNotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use function PHPUnit\Framework\isInstanceOf;
use function call_user_func;
use function gettype;
use function is_callable;
use function is_null;
use function is_numeric;
use function is_scalar;


/**
 * Class Framework\Container
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-consistent-constructor
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 *
 */
class Container implements ContainerInterface
{
    /**
     * The current globally available container (if any).
     *
     * @var Container|null
     */
    protected static $instance;

    /**
     * @var array mixed $services
     */
    private mixed $services = [];

    public static function reset(): void {
        self::$instance = null;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerNotFoundException No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id) : mixed
    {
        $item = $this->resolve($id);

        if ($item instanceof ReflectionFunction) {
            return $this->callClosure($item);
        } elseif ($item instanceof ReflectionClass) {
            return $this->createInstance($item);
        } else {
            return $item;
        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     *
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    public function has(string $id)
    {
        try {
            $this->resolve($id);
            return true;
        } catch (ContainerNotFoundException $e) {
            return false;
        }
    }

    /**
     * @return mixed
     *
     * @psalm-return mixed
     *
     * @throws \Framework\Exceptions\ContainerException
     * @throws \Framework\Exceptions\ContainerNotFoundException
     */
    private function resolve(string $id)
    {
        try {
            $name = $id;
            if (isset($this->getInstance()->services[$id])) {
                $name = $this->getInstance()->services[$id];
                if (is_callable($name)) {
                    /** @psalm-suppress InvalidArgument */
                    return (new ReflectionFunction($name));
                } elseif (
                    (gettype($name) == "string" && strpos($name, "class@anonymous") !== 0) ||
                    is_numeric($name) ||
                    is_array($name)
                )
                {
                    return $name;
                }
            }
            return (new ReflectionClass($name));
        } catch (ReflectionException $e) {
            throw new ContainerNotFoundException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    private function createInstance(ReflectionClass $item): object
    {
        $constructor = $item->getConstructor();
        if (is_null($constructor) || 0 == $constructor->getNumberOfRequiredParameters()) {
            return $item->newInstance();
        }
        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $params[] = $this->get($param->getName());
        }

        return $item->newInstanceArgs($params);
    }

    /**
     * @return mixed Returns the result of the invoked function call.
     */
    private function callClosure(ReflectionFunction $item) : mixed
    {
        return $item->invoke(...$this->constructArguments($item->getParameters()));
    }

    private function constructArguments(array $arguments) : array
    {
        $params = [];
        foreach ($arguments as $param) {
            if ($name = $param->getName()) {
                $params[] = $this->get($name);
            }
        }

        return $params;
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value) : Container
    {
        $this->getInstance()->services[$key] = $value;
        return $this;
    }

    /*
     * @return Container
     */
    public static function getInstance() : Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}
