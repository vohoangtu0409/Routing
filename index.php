<?php
require 'vendor/autoload.php';
$router = new \Tuezy\Routing\Router();
$router->get('/', function(){
    return 'Index home';
})->name('home');

$router->get('/{id}', function(){
    return 'Index home';
})->name('home.account');

$router->group('admin', function($router){
    $router->get('index', function(){
        return 'Admin Index Callback';
    })->name('index');
    $router->get('/', function(){
        return 'Admin Index Callback';
    })->name('admin.index');
    $router->get('edit/{id}', [
        'controller' => 'AdminController',
        'action'    => 'index'
    ])->name('admin.edit');

    $router->get('edit/{id}/account/{accountId}', [
        'controller' => 'AdminController',
        'action'    => 'account'
    ])->name('admin.edit.account');
});
echo '<pre>';
dd($router->match($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']));
echo '</pre>';