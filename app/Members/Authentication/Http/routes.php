<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of Storgman.
 *
 * Storgman is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Storgman is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Storgman.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Storgman
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'auth', 'namespace' => 'Members\Authentication\Http\Controllers'], function (Router $router) {

    $router->get('/',                  ['as' => 'auth',                   'uses' => 'AuthController@index',                'middleware' => 'guest']);
    $router->post('/',                 ['as' => 'postAuth',               'uses' => 'AuthController@login',                'middleware' => 'guest']);
    $router->get('/logout',            ['as' => 'logout',                 'uses' => 'AuthController@logout',               'middleware' => 'auth']);
    $router->get('/facebook',          ['as' => 'auth.facebook',          'uses' => 'AuthController@loginWithFacebook',    'middleware' => 'guest']);
    $router->get('/facebook-callback', ['as' => 'auth.facebook-callback', 'uses' => 'AuthController@proceedFacebookLogin', 'middleware' => 'guest']);

});
