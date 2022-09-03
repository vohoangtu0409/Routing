<?php
require 'vendor/autoload.php';


$router = new \Tuezy\Router\Router();

$router->get('/', function(){
    return 'Index home';
})->name('home');

$router->get('/{id}', function(){
    return 'Index {id}  home';
})->name('home.account');

$router->get('/contact', function(){
    return 'contact home';
})->name('home.contact');
for($i = 0; $i < 100000; $i++) {
    $router->group('admin'. ($i == 0 ? '' : $i), function ($router) {
        $router->get('index', function () {
            return 'Admin Index Callback';
        })->name('index');

        $router->get('/', function () {
            return 'Admin Index Callback';
        })->name('admin.index');

        $router->get('edit/{id}', [
            'controller' => 'AdminController',
            'action' => 'index'
        ])->name('admin.edit');

        $router->get('edit/{id}/account/{accountId}', [
            'controller' => 'AdminController',
            'action' => 'account'
        ])->name('admin.edit.account');
    });
}
$router->get('/contact/{id}', function(){
    return 'contact {id} home';
})->name('home.contact.index');

$router->get('/admin/edit/{id}', function(){
    return 'admin edit {id} home';
})->name('admin.contact.index');

$microtime = microtime();


echo '<pre>';
$route = $router->match($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
dump($route);

$action = $route->getAction();

if(is_callable($action)){
    echo call_user_func($action);
}else{
    dd($action);
}

echo '</pre>';

echo (microtime() - $microtime);