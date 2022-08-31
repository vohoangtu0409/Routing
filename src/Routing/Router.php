<?php
namespace Tuezy\Routing;

use Tuezy\Routing\Contract\RouterInterface;

class Router implements RouterInterface {

    protected $method;
    protected $uri;
    protected $action;
    protected $prefix = '/index';
    protected $group = false;

    protected $isGrouping = 0;

    protected $routes = [];

    protected $currentRoute;



    protected function current(): CompiledRoute{
        return  $this->currentRoute;
    }

    public function reset(){
        $this->isGrouping = 0;
        $this->uri = null;
        $this->action = null;
        $this->prefix = '/index';
        $this->group = false;
    }

    private function defineRoute($method, $uri, $action){
        $compiledRoute = new CompiledRoute(
            new Route($method, $this->startWithSlash($uri), $action),
            $this->prefix
        );

        $this->method = $method;
        $this->currentRoute = $compiledRoute;
        $this->routes[$method][$compiledRoute->getName()] = $compiledRoute;

        if($this->isGrouping == 0)
            $this->reset();

        return $compiledRoute;
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

    public function group(string $prefix, $action = null){
        $this->isGrouping = 1;
        $this->prefix = $this->startWithSlash($prefix);

        if(is_callable($action)){
            call_user_func($action, $this);
        }
        $this->reset();
        return $this;
    }

    public function prefix(string $prefix, $action = null){
        return $this->group($prefix, $action);
    }

    public function name(string $name){
        $route = $this->current();
        $this->routes[$this->method][$name] = $route;
        return $this;
    }

    public function routes()
    {
        return $this->routes;
    }

    public function match($method, $uri){
        //check $uri startWith /
        $uriParts = explode('/', $uri);

        $prefix = '';
        if($uriParts[1] == $uriParts[0]){
            $prefix = '/index';
        }

        $generateName = $method . $prefix . $this->startWithSlash($uri);

        if(isset($this->routes[$method][$generateName])){
            return $this->routes[$method][$generateName];
        }else{

        }

        return [$generateName, $this->routes[$method]];
    }

    private function startWithSlash($name){
        return substr($name,0,1) != '/' ? '/' . $name : $name;
    }
}