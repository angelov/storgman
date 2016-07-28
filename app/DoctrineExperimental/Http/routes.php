<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'd', 'namespace' => 'DoctrineExperimental\Http\Controllers'], function (Router $router) {

    $router->get('/', ['as' => 'd.index', 'uses' => 'IndexController@index']);

});
