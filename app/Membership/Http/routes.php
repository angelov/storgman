<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of EESTEC Platform.
 *
 * EESTEC Platform is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EESTEC Platform is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EESTEC Platform.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EESTEC Platform
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'fees', 'middleware' => ['auth', 'boardMember'], 'namespace' => 'Membership\Http\Controllers'], function (Router $router) {

    $router->get('/',        ['as' => 'fees.index',   'uses' => 'FeesController@index']);
    $router->get('/archive', ['as' => 'fees.archive', 'uses' => 'FeesController@archive']);
    $router->get('/create',  ['as' => 'fees.create',  'uses' => 'FeesController@create',  'middleware' => 'ajax']);
    $router->post('/',       ['as' => 'fees.store',   'uses' => 'FeesController@store',   'middleware' => 'ajax']);
    $router->delete('/{id}', ['as' => 'fees.destroy', 'uses' => 'FeesController@destroy', 'middleware' => 'ajax']);

});
