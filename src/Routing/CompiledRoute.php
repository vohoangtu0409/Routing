<?php
namespace Tuezy\Routing;

class CompiledRoute{

    protected $route;

    protected $prefix;

    protected $name;

    protected $patternCompiled = '({+[a-zA-Z0-9]+})';

    protected $reversePattern = '[A-Za-z0-9]';

    /**
     * @param $route
     */
    public function __construct(Route $route, $prefix = 'index')
    {
        $this->route = $route;
        $this->prefix = $prefix;
        $this->name = $this->generateName();
    }

    /**
     * @return mixed
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute(Route $route): void
    {
        $this->route = $route;
    }

    public function name($name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    private function generateName(){
        return $this->route->getMethod() . $this->prefix . $this->compiled($this->route->getPath());
    }

    private function compiled($path){
        $explodedURI = explode('/', $path);
        foreach ($explodedURI as $stt => $uri){
            if(substr($uri , 0 , 1) == '{'){
                $explodedURI[$stt]  = $this->reversePattern;
            }
        }
        return implode('/', $explodedURI);
    }

}