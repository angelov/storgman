<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'meetings', 'middleware' => 'auth', 'namespace' => 'Meetings\Http\Controllers'], function (Router $router) {

    $router->get('/',          ['as' => 'meetings.index',   'uses' => 'MeetingsController@index']);
    $router->get('/{id}',      ['as' => 'meetings.show',    'uses' => 'MeetingsController@show']);

    $router->group(['middleware' => 'boardMember'], function (Router $router) {

        $router->get('/create',    ['as' => 'meetings.create',  'uses' => 'MeetingsController@create']);
        $router->post('/',         ['as' => 'meetings.store',   'uses' => 'MeetingsController@store']);
        $router->get('/{id}/edit', ['as' => 'meetings.edit',    'uses' => 'MeetingsController@edit']);
        $router->put('/{id}',      ['as' => 'meetings.update',  'uses' => 'MeetingsController@update']);
        $router->delete('/{id}',   ['as' => 'meetings.destroy', 'uses' => 'MeetingsController@destroy']);

        $router->get('/{id}/report',  ['as' => 'meetings.reports.create', 'uses' => 'ReportsController@create']);
        $router->post('/{id}/report', ['as' => 'meetings.reports.store',  'uses' => 'ReportsController@store']);

    });

    $router->get('/{id}/attachments', ['as' => 'meetings.attachments.index',  'uses' => 'AttachmentsController@index']);

});
