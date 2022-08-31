<?php
namespace Tuezy\Routing;

class Route{

    protected $method;

    protected $path;

    protected $namespace;

    protected $action;

    protected $name;

    public const ROUTE_GET = 'GET';

    public const ROUTE_POST = 'POST';

    public const ROUTE_PUT = 'PUT';

    /**
     * @param $method
     * @param $path
     * @param $namespace
     * @param $action
     * @param $name
     */
    public function __construct($method, $path, $action)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action): void
    {
        $this->action = $action;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }







}