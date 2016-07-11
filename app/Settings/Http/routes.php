<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'settings', 'namespace' => 'Settings\Http\Controllers', 'middleware' => ['auth', 'boardMember']], function (Router $router) {

    $router->get('/', ['as' => 'settings.index', 'uses' => 'SettingsController@index']);

    $router->group(['prefix' => 'faculties'], function (Router $router) {

        $router->get( '/', ['as' => 'settings.faculties.index',                         'uses' => 'FacultiesController@index']);
        $router->post('/', ['as' => 'settings.faculties.store', 'middleware' => 'ajax', 'uses' => 'FacultiesController@store']);

        $router->delete('/{id}', ['as' => 'settings.faculties.delete', 'middleware' => 'ajax', 'uses' => 'FacultiesController@delete']);

        $router->get('/{id}/edit', ['as' => 'settings.faculties.edit',   'middleware' => 'ajax', 'uses' => 'FacultiesController@edit']);
        $router->put('/{id}',      ['as' => 'settings.faculties.update', 'middleware' => 'ajax', 'uses' => 'FacultiesController@update']);

        $router->post('/{id}/enable',  ['as' => 'settings.faculties.enable',  'middleware' => 'ajax', 'uses' => 'FacultiesController@enable']);
        $router->post('/{id}/disable', ['as' => 'settings.faculties.disable', 'middleware' => 'ajax', 'uses' => 'FacultiesController@disable']);

    });

});
