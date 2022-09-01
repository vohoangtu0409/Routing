<?php
namespace Tuezy\Routing;

use Tuezy\Routing\Traits\ValidateRoute;

class Router{
    use ValidateRoute;

    protected $prefix = '/index';

    protected $grouping = 0;

    protected $currentRoute;

    protected $routes = [];
    protected $routesMaps = [];


    public function group($prefix, $action){
        return $this->prefix($prefix, $action);
    }
    public function prefix($prefix, $action){
      $this->grouping = 1;
      $this->prefix = $this->forceStartWith($prefix == '/' ? 'index' : $prefix, '/');
      $this->whiteList($this->prefix);
      call_user_func($action, $this);
      $this->prefix = '/index';
      $this->grouping = 0;
      return $this;
    }
    private function definePrefix($uri){
        if($this->grouping){
            return $this->prefix;
        }
        if($uri == '/') return '/index';
        else{
            $uri = explode('/', $uri);
            if(substr($uri[1], 0, 1) != '{'){
                return $this->forceStartWith($uri[1], '/');
            }
        }
        return '/index';
    }

    private function genURI($uri){
        $uri = $this->forceStartWith($uri, '/');
        $this->prefix = $this->definePrefix($uri);
        $genURI = $this->prefix . $uri;
        if(substr($genURI, -1, 1) == '/')
            return substr($genURI, 0, strlen($genURI) - 1);
        return $genURI;
    }
    private function defineRoute($method, string $uri, $action){
        $this->currentRoute = new Route($method, $this->genURI($uri), $action);
        $this->routes[$method][$this->currentRoute->getCompiledName()] = $this->currentRoute;
        return $this;
    }

    public function get(string $uri, $action){
        return $this->defineRoute(Route::ROUTE_GET, $uri, $action);
    }

    public function post(string $uri, $action){
        return $this->defineRoute(Route::ROUTE_POST, $uri, $action);
    }

    public function put(string $uri, $action){
        return $this->defineRoute(Route::ROUTE_PUT, $uri, $action);
    }

    public function name($name){
        $this->routesMaps[$name] = $this->currentRoute->getCompiledName();
    }

    public function match($method, $uri){
        $uri = $this->forceStartWith($uri, '/');

        if(substr($uri, -1, 1) == '/')
            $uri = substr($uri, 0, strlen($uri) - 1);

        $uriParts = explode('/', $uri);

        $prefix = '';
        if($uriParts[1] == $uriParts[0]){
            $prefix = '/index';
        }

        foreach ($uriParts as $part){
            if(in_array($part, array_keys(self::$whiteList))) continue;
            $uri = str_replace($part, Route::$reversePattern, $uri);
        }

        $generateName = $method . $prefix . $uri;

        if(isset($this->routes[$method][$generateName])){
            return $this->routes[$method][$generateName];
        }
        return null;
    }
}