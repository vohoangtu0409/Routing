<?php
namespace Tuezy\Router;

class Router{
    protected $isGrouping = 0;

    protected static $routes = [];

    protected $routesMap = [];

    protected $excludeKeys = [];

    protected $prefix;

    protected $debug = 0;

    protected $isCache;

    public function __construct()
    {
        $this->exclude('GET');
        $this->exclude('POST');
        $this->exclude('PUT');
    }

    public function group($prefix, $callable = null){
        $this->prefix($prefix, $callable);
    }

    public function prefix($prefix, $callable = null){
        if($this->isCache){
            return $this;
        }
        $this->prefix = RouterHelper::removeSlash($prefix);
        $this->isGrouping = 1;
        $this->exclude($prefix);
        if($callable)
            call_user_func($callable, $this);
        $this->isGrouping = 0;
    }

    private function create($method, $uri, $action){
        $uri = RouterHelper::startWithSlash($uri);

        if($this->isGrouping == 1){
            $prefix = $this->prefix;
        }else{
            $prefix = RouterHelper::calcPrefix($uri);
            $this->exclude($prefix);
        }
        $this->whiteListKey($uri);

        $route = new Route($method, $prefix, $uri, $action);
        $compiledName = RouterHelper::computeCompiledName($method, $prefix, $uri);
        $route->setCompiledName($compiledName);


        $this->routes[$method][$route->getCompiledName()] = $route;
        return $this;
    }

    public function get($uri, $action){
        return $this->create('GET', $uri, $action);
    }
    public function post($uri, $action){
        return $this->create('POST', $uri, $action);
    }
    public function put($uri, $action){
        return $this->create('PUT', $uri, $action);
    }

    public function name($name){

    }

    private function whiteListKey($uri){
        if(strlen($uri) > 1){
            $explodeURI = explode('/', $uri);
            foreach($explodeURI as $euri){
                if(RouterHelper::isStartWith($euri , '{') )
                    continue;
                if(!empty($euri))
                    $this->exclude($euri);
            }

        }
    }

    public function match($method, $uri){
        $uri = RouterHelper::startWithSlash($uri);
        if($uri != '/' && RouterHelper::isEndWith($uri, '/')){
            $uri = substr($uri, 0 , strlen($uri)-1);
        }

        $prefix = RouterHelper::calcPrefix($uri);

        $computeCompiledName = RouterHelper::computeCompiledName($method, $prefix, $uri);

        $encodeURI = explode('/', $computeCompiledName);

        foreach ($encodeURI as $index => $euri){
            if( !empty($euri) && ! in_array($euri, array_keys($this->excludeKeys))){
                $encodeURI[$index] = '[a-zA-Z0-9]';
            }
        }

        $computeCompiledName = implode('/', $encodeURI);
        if(isset($this->routes[$method][$computeCompiledName])){
            return $this->routes[$method][$computeCompiledName];
        }
        return null;
    }

    private function exclude($key){
        if(!RouterHelper::isStartWith($key, '{'))
            $this->excludeKeys[$key] = 1;
    }

    /**
     * @param array $routes
     */
    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    /**
     * @param array $routesMap
     */
    public function setRoutesMap(array $routesMap): void
    {
        $this->routesMap = $routesMap;
    }

    /**
     * @param array $excludeKeys
     */
    public function setExcludeKeys(array $excludeKeys): void
    {
        $this->excludeKeys = $excludeKeys;
    }

    /**
     * @param mixed $isCache
     */
    public function setIsCache($isCache): void
    {
        $this->isCache = $isCache;
    }


}