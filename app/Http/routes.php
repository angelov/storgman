<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Illuminate\Routing\Router;

/** @var Router $router */

// TEMPORARY WORKAROUND!
// @todo remove when the "intervention/image*" packages are fixed for laravel 5
$router->get('/imgtmp', ['as' => 'imagecache', function() { return; }]);

/**
 * Global patterns
 */

$router->pattern('id', '[0-9]+');

/**
 * Dashboard
 */

$router->get('/', ['as' => 'homepage', 'uses' => 'HomeController@showHomepage', 'middleware' => 'auth']);

/**
 * Authentication
 */

$router->group(['prefix' => 'auth'], function (Router $router) {

    $router->get('/',
        ['as' => 'auth',
         'uses' => 'AuthController@index',
         'middleware' => 'guest']
    );
    $router->post('/',
        ['as' => 'postAuth',
         'uses' => 'AuthController@login',
         'middleware' => 'guest']
    );
    $router->get('/logout',
        ['as' => 'logout',
         'uses' => 'AuthController@logout',
         'middleware' => 'auth']
    );

});

/**
 * Members management
 */

$router->group(['prefix' => 'members'], function (Router $router) {

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

/**
 * Membership fees management
 */

$router->group(['prefix' => 'fees', 'middleware' => ['auth', 'boardMember']], function (Router $router) {

    $router->get('/',
        ['as' => 'fees.index',
         'uses' => 'FeesController@index']
    );
    $router->get('/archive',
        ['as' => 'fees.archive',
         'uses' => 'FeesController@archive']
    );
    $router->get('/create',
        ['as' => 'fees.create',
         'uses' => 'FeesController@create',
         'middleware' => 'ajax']
    );
    $router->post('/',
        ['as' => 'fees.store',
         'uses' => 'FeesController@store',
         'middleware' => 'ajax']
    );
    $router->delete('/{id}',
        ['as' => 'fees.destroy',
         'uses' => 'FeesController@destroy',
         'middleware' => 'ajax']
    );

});

/**
 * Meetings management
 */

$router->group(['prefix' => 'meetings', ['auth', 'boardMember']], function (Router $router) {

    $router->get('/',          ['as' => 'meetings.index',   'uses' => 'MeetingsController@index']);
    $router->get('/create',    ['as' => 'meetings.create',  'uses' => 'MeetingsController@create']);
    $router->post('/',         ['as' => 'meetings.store',   'uses' => 'MeetingsController@store']);
    $router->get('/{id}',      ['as' => 'meetings.show',    'uses' => 'MeetingsController@show']);
    $router->get('/{id}/edit', ['as' => 'meetings.edit',    'uses' => 'MeetingsController@edit']);
    $router->put('/{id}',      ['as' => 'meetings.update',  'uses' => 'MeetingsController@update']);
    $router->delete('/{id}',   ['as' => 'meetings.destroy', 'uses' => 'MeetingsController@destroy']);

});
