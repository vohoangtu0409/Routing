<?php
require 'vendor/autoload.php';


$router = new \Tuezy\Router\Router();

$router->group('dashboard', function($router){
    $router->create('GET', '/', [
        'controller' => IndexController::class,
        'action'    => 'index',
        'default'   => [

        ]
    ]);
    $router->create('GET', '/{id}', 'abc')->name('dashboard.index.id');
    $router->create('GET', '/account', 'abc');
    $router->create('GET', '/account/{id}', 'abc');
    $router->create('GET', '/', 'abc');
});
//
//
$router->create('GET', '/', 'abc')->name('index');
$router->name('index.id')->create('GET', '/{id}', 'abc');
$router->create('GET', '/account', 'abc');
$router->name('index.count')->create('GET', '/account/{id}', 'abc');
$router->create('GET', '/', 'abc');

$request_uri = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : '/';
$request = $router->match($_SERVER['REQUEST_METHOD'],$request_uri);

dump($request, $router->all());

$all = $router->all();


dump($router->route('index'));
dump($router->route('index.id'));
dump($router->route('dashboard.index.id'));
dump($router->route('index.count'));

