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
        $prefix = '/index';

        if($this->grouping){
            $prefix =  $this->prefix;
            if($uri == '/'){
                $prefix = '/index';
            }
        }else{
            if($uri != '/'){
                $uris = explode('/', $uri);

                if(substr($uris[1], 0, 1) != '{'){
                    $prefix = $this->forceStartWith($uris[1], '/');
                }
            }
        }

        if($prefix == $uri) $prefix = '/index';

        $this->whiteList($prefix);

        return $prefix;
    }

    private function genURI($uri){

        $prefix = $this->definePrefix($uri);
        echo 'working with: '. $prefix . $uri . '<br />';
        if($this->grouping && $prefix != $this->prefix){ //in case grouping with uri '/'
            $uri = $this->prefix;
        }

        $genURI = $prefix . $uri;
        echo 'Result working with: '. $prefix . $uri . '<br />';
        if(substr($genURI, -1, 1) == '/')
            return substr($genURI, 0, strlen($genURI) - 1);
        return $genURI;
    }

    private function defineRoute($method, string $uri, $action){
        $uri = $this->forceStartWith($uri, '/');
        $prefix = $this->definePrefix($uri);

        $this->currentRoute = new Route($method, $prefix, $this->genURI($uri), $action);
        $this->currentRoute->setPrefix($this->prefix);
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
        $this->currentRoute->setName($name);
        $this->routesMaps[$name] = $this->currentRoute->getCompiledName();
    }

    public function match($method, $uri){
        echo (json_encode($this->routes));
        $uri = $this->forceStartWith($uri, '/');
        if(substr($uri, -1, 1) == '/')
            $uri = substr($uri, 0, strlen($uri) - 1);

        if(strlen($uri) == 0){
            $prefix = '/index';
        }else{
            $uriParts = explode('/', $uri);

            $prefix = $this->forceStartWith($uriParts[1] , '/');

            echo 'input working:'. $prefix.'<br />';

            if($uriParts[1] == $uriParts[0]){
                $prefix = '/index';
            }

            foreach ($uriParts as $part){
                if(in_array($part, array_keys(self::$whiteList))) continue;
                $uri = str_replace($part, Route::$reversePattern, $uri);
            }
        }
        echo 'input $prefix working:'. $prefix.'<br />';
        if(!in_array($prefix, array_keys(self::$whiteList)))
            $prefix = '/index';

        $generateName = $method . $prefix . $uri;

        echo 'input working:'. $generateName.'<br />';

        if(isset($this->routes[$method][$generateName])){
            return $this->routes[$method][$generateName];
        }
        return null;
    }
}