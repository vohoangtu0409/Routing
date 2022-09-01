<?php
namespace Tuezy\Routing;

class Route{

    protected $method;

    protected $prefix;

    protected $uri;

    protected $namespace;

    protected $action;

    protected $name;

    protected $compiledName;

    public const ROUTE_GET = 'GET';

    public const ROUTE_POST = 'POST';

    public const ROUTE_PUT = 'PUT';

    public static $reversePattern = '[A-Za-z0-9]';

    /**
     * @param $method
     * @param $prefix
     * @param $uri
     * @param $namespace
     * @param $action
     * @param $name
     */
    public function __construct($method, $uri, $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->generateCompiledName();
    }

    private function generateCompiledName(){
        $compoledURI = explode('/', $this->uri);
        foreach ($compoledURI as $stt => $uri){
            if(substr($uri, 0 , 1) == '{'){
                $compoledURI[$stt] = self::$reversePattern;
            }
            else if(!empty($uri)){
                Router::$whiteList[$uri] = 1;
            }
        }
        $this->compiledName = $this->method . implode('/', $compoledURI);
    }
    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method): void
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action): void
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCompiledName()
    {
        return $this->compiledName;
    }

}