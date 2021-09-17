<?php

namespace Blog\Infrastructures;

use ReflectionClass;
use ReflectionFunction;
use ReflectionException;
use Psr\Container\ContainerInterface;
use Blog\Exceptions\{ContainerException,
                     ContainerNotFoundException};

/**
* Class Blog\Infrastructures\Container
* @author Erwin Pakpahan <erwinmaruli@live.com>
*
* @psalm-consistent-constructor
*/
class Container implements ContainerInterface
{
    /**
     * Container will be globally shared
     *
     * @var Container|null
     */
    protected static $instance;

    /*
     * @return an instance of Container
     */
    public static function getInstance() : Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Fetch entry of the container by its identifier
     *
     * @param string $id Identifier of the entry to look for
     *
     * @throws \Blog\Exceptions\ContainerNotFoundException No entry was found
     * @throws \Blog\Exceptions\ContainerException Error while retrieving the entry
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
     * Returns true if the container can return an entry
     * Returns false otherwise
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws \Blog\Exceptions\ContainerException
     * @throws \Blog\Exceptions\ContainerNotFoundException
     *
     * @return bool
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
     * Set entry by unique identifier
     * This method can be chained call
     *
     * @param mixed $value
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress UndefinedPropertyFetch
     * @psalm-suppress UndefinedPropertyAssignment
     *
     * @return Container
     */
    public function set(string $key, $value) : Container
    {
        $this->getInstance()->services[$key] = $value;
        return $this;
    }

    /**
     * @return mixed
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress UndefinedPropertyFetch
     *
     * @throws \Blog\Exceptions\ContainerException
     * @throws \Blog\Exceptions\ContainerNotFoundException
     */
    private function resolve(string $id) : mixed
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
            /** @psalm-suppress MixedArgument */
            return (new ReflectionClass($name));
        } catch (ReflectionException $e) {
            throw new ContainerNotFoundException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Build an instance of resolved class entry
     *
     * @psalm-suppress MixedAssignment
     *
     * @return object
     */
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
     * Returns the result of the invoked function call
     *
     * @return mixed
     */
    private function callClosure(ReflectionFunction $item) : mixed
    {
        return $item->invoke(...$this->constructArguments($item->getParameters()));
    }

    /**
     * Build arguments for resolved entry
     *
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress MixedAssignment
     *
     * @return array
     */
    private function constructArguments(array $arguments) : array
    {
        $params = [];
        foreach ($arguments as $param) {
            /** @var string */
            if ($name = $param->getName()) {
                /** @var mixed */
                $params[] = $this->get((string) $name);
            }
        }

        return $params;
    }
}
