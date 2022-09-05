<?php
namespace Tuezy\Router;

use Closure;
class Router{

  protected $prefix = '';
  protected $isGrouping = 0;
  protected $routeList = [];
  protected $routeMapList = [];
  protected $whiteList = [];
  protected $current;
  protected $name = '';
  protected $currentStep = 0;
  protected $previousStep = 0;

  public const STEP_GROUP = 1;
  public const STEP_CREATE = 2;
  public const STEP_NAME = 3;

  public function group($prefix, Closure $callback){
      $this->allowStep(self::STEP_GROUP);
      $this->prefix($prefix);
      $this->isGrouping = 1;
      call_user_func($callback, $this);
      $this->isGrouping = 0;
      $this->prefix = '';
  }

  public function prefix($prefix){
      $this->prefix = $prefix;
      return $this;
  }
  public function create($method, $uri, $action){
      $this->allowStep(self::STEP_CREATE);
      $route = new Route($method, $this->calcPrefix($uri), $action);
      $route->setCompiledName($this->generateKey($route));

      $this->current = $route;
      $this->addToList($route);

      if($this->isGrouping == 0){
          $this->prefix = '';
      }

      if(!empty($this->name)){
          $this->name($this->name);
      }
      return $this;
  }

  public function name($name){
      $this->allowStep(self::STEP_NAME);
      $this->name = $name;

      if(isset($this->routeMapList[$name])){
        throw new \Exception("Name is already existed");
      }
      if(is_null($this->current)){
          return $this;
      }else{
          $this->routeMapList[$this->name]= $this->current->getCompiledName();
          $this->current = null;
          $this->name = '';
      }
      return $this;
  }

  public function all(){
      return [$this->routeList, $this->whiteList, $this->routeMapList];
  }

  public function match($method, $uri) : Route{
      if($uri != '/' && RouterHelper::isEndWith($uri, '/')){
          $uri = substr($uri, 0, strlen($uri) - 1);
      }
      $explodedURI = explode('/', $uri);
      foreach($explodedURI as $i => $eri){
          if(!empty($eri) && !in_array($eri, array_keys($this->whiteList))){
              $explodedURI[$i] = '[a-zA-Z0-9]';
          }
      }
      $rri = implode('/', $explodedURI);
      return $this->routeList[$method . $rri];
  }

  private function calcPrefix($uri){
      if(!empty($this->prefix)){
        $this->prefix = RouterHelper::startWithSlash($this->prefix);
      }

      $uri = RouterHelper::startWithSlash($uri);

      if(!empty($this->prefix) && $uri == '/'){
        $uri = '';
      }

      return $this->prefix . $uri;
  }

  private function addToList(Route $route){
    $this->routeList[$route->getCompiledName()] = $route;
  }

  private function generateKey(Route $route)
  {
      $uri = $route->getUri();
      $uri = RouterHelper::compiled($uri);
      $explodedURI = explode('/', $uri);
      foreach($explodedURI as $i => $eri){
        if(!empty($eri) && $eri != '[a-zA-Z0-9]'){
            if(!isset($this->whiteList[$eri]))
                $this->whiteList[$eri] = 1;
        }
      }
      return $route->getMethod(). $uri;
  }

  private function allowStep($step){
      if($this->previousStep == $step){
          switch ($step){
              case self::STEP_NAME:
              case self::STEP_CREATE:
                  $this->reset();
                  break;
          }

      }
      $this->previousStep = $this->currentStep;
      $this->currentStep = $step;
      return true;
  }

  private function reset(){
      $this->name = '';
      $this->currentStep = 0;
      $this->previousStep = 0;
  }

  public function route($name){
      if(isset($this->routeMapList[$name])){
          return $this->routeList[$this->routeMapList[$name]];
      }
      return null;
  }
}