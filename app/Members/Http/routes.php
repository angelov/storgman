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

$this->group(['namespace' => 'Members\Http\Controllers'], function(Router $router) {
    $router->get('/', ['as' => 'homepage', 'uses' => 'HomeController@showHomepage', 'middleware' => 'auth']);
});

$router->group(['prefix' => 'members', 'namespace' => 'Members\Http\Controllers'], function (Router $router) {

    $router->group(['middleware' => ['auth', 'boardMemberOrSelf']], function(Router $router) {
        $router->get('/{id}',      ['as' => 'members.show',   'uses' => 'MembersController@show']);
        $router->get('/{id}/edit', ['as' => 'members.edit',   'uses' => 'MembersController@edit']);
        $router->put('/{id}',      ['as' => 'members.update', 'uses' => 'MembersController@update']);
    });

    $router->group(['middleware' => 'guest'], function(Router $router) {
        $router->get('/register',
            ['as' => 'members.register',
                'uses' => 'MembersController@register']
        );
        $router->post('/register',
            ['as' => 'members.postRegister',
                'uses' => 'MembersController@postRegister']
        );
    });

    $router->group(['middleware' => ['auth', 'boardMember']], function(Router $router) {
        $router->get('/',          ['as' => 'members.index',  'uses' => 'MembersController@index']);
        $router->get('/create',    ['as' => 'members.create', 'uses' => 'MembersController@create']);
        $router->post('/',         ['as' => 'members.store',  'uses' => 'MembersController@store']);
        $router->delete('/{id}',
            ['as' => 'members.destroy',
                'uses' => 'MembersController@destroy',
                'middleware' => 'ajax']
        );
        $router->get('/prefetch',
            ['as' => 'members.prefetch',
                'uses' => 'MembersController@prefetch',
                'middleware' => 'ajax']
        );
        $router->get('/board',     ['as' => 'members.board',  'uses' => 'MembersController@board']);
        $router->get('/{id}/quick-info',
            ['as' => 'members.quick',
                'uses' => 'MembersController@quickMemberInfo',
                'middleware' => 'ajax']
        );
        $router->post('/{id}/approve',
            ['as' => 'members.approve',
                'uses' => 'MembersController@approve',
                'middleware' => 'ajax']
        );
        $router->post('/{id}/decline',
            ['as' => 'members.decline',
                'uses' => 'MembersController@decline',
                'middleware' => 'ajax']
        );
        $router->get('/unapproved', ['as' => 'members.unapproved',  'uses' => 'MembersController@unapproved']);
    });

});