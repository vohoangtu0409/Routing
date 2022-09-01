<?php
namespace Tuezy\Routing\Contract;

interface RouterInterface{
    public function get(string $uri, $action);
    public function post(string $uri, $action);
    public function put(string $uri, $action);
    public function prefix(string $prefix, $action);
    public function group(string $prefix, $action);
    public function name(string $name);
}