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

$router->group(['prefix' => 'documents', 'namespace' => 'Documents\Http\Controllers'], function (Router $router) {

    $router->get('/',          ['as' => 'documents.index',   'uses' => 'DocumentsController@index']);
    $router->post('/',         ['as' => 'documents.store',   'uses' => 'DocumentsController@store',   'middleware' => ['ajax']]);
    $router->get('/{id}',      ['as' => 'documents.show',    'uses' => 'DocumentsController@show']);
    $router->delete('/{id}',   ['as' => 'documents.destroy', 'uses' => 'DocumentsController@destroy', 'middleware' => ['ajax']]);
    $router->get('/{id}/edit', ['as' => 'documents.edit',    'uses' => 'DocumentsController@edit',    'middleware' => ['ajax']]);
    $router->put('/{id}',      ['as' => 'documents.update',  'uses' => 'DocumentsController@update',  'middleware' => ['ajax']]);
    $router->get('/tag/{id}',  ['as' => 'documents.byTag',   'uses' => 'DocumentsController@byTag']);
    $router->get('/tags',      ['as' => 'documents.tags',    'uses' => 'DocumentsController@listTags']);

});
