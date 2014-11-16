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

/**
 * Global patterns
 */

Route::pattern('id', '[0-9]+');

/**
 * Dashboard
 */

Route::get('/', ['as' => 'homepage', 'uses' => 'HomeController@showHomepage', 'middleware' => 'auth']);

/**
 * Authentication
 */

Route::group(['prefix' => 'auth'], function () {

    Route::get('/',
        ['as' => 'auth',
         'uses' => 'AuthController@index',
         'middleware' => 'guest']
    );
    Route::post('/',
        ['as' => 'postAuth',
         'uses' => 'AuthController@login',
         'middleware' => 'guest']
    );
    Route::get('/logout',
        ['as' => 'logout',
         'uses' => 'AuthController@logout',
         'middleware' => 'auth']
    );

});

/**
 * Members management
 */

Route::group(['prefix' => 'members'], function () {

    Route::group(['middleware' => 'guest'], function() {
        Route::get('/register',
            ['as' => 'members.register',
             'uses' => 'MembersController@register']
        );
        Route::post('/register',
            ['as' => 'members.postRegister',
             'uses' => 'MembersController@postRegister']
        );
    });

    Route::group(['middleware' => 'auth|boardMember'], function() {
        Route::get('/',          ['as' => 'members.index',  'uses' => 'MembersController@index']);
        Route::get('/create',    ['as' => 'members.create', 'uses' => 'MembersController@create']);
        Route::post('/',         ['as' => 'members.store',  'uses' => 'MembersController@store']);
        Route::get('/{id}',      ['as' => 'members.show',   'uses' => 'MembersController@show']);
        Route::get('/{id}/edit', ['as' => 'members.edit',   'uses' => 'MembersController@edit']);
        Route::put('/{id}',      ['as' => 'members.update', 'uses' => 'MembersController@update']);
        Route::delete('/{id}',
            ['as' => 'members.destroy',
             'uses' => 'MembersController@destroy',
             'middleware' => 'ajax']
        );
        Route::get('/prefetch',
            ['as' => 'members.prefetch',
             'uses' => 'MembersController@prefetch',
             'middleware' => 'ajax']
        );
        Route::get('/board',     ['as' => 'members.board',  'uses' => 'MembersController@board']);
        Route::get('/{id}/quick-info',
            ['as' => 'members.quick',
             'uses' => 'MembersController@quickMemberInfo',
             'middleware' => 'ajax']
        );
        Route::post('/{id}/approve',
            ['as' => 'members.approve',
             'uses' => 'MembersController@approve',
             'middleware' => 'ajax']
        );
        Route::post('/{id}/decline',
            ['as' => 'members.decline',
             'uses' => 'MembersController@decline',
             'middleware' => 'ajax']
        );
        Route::get('/unapproved', ['as' => 'members.unapproved',  'uses' => 'MembersController@unapproved']);
    });

});

/**
 * Membership fees management
 */

Route::group(['prefix' => 'fees', 'middleware' => 'auth|boardMember'], function () {

    Route::get('/',
        ['as' => 'fees.index',
         'uses' => 'FeesController@index']
    );
    Route::get('/archive',
        ['as' => 'fees.archive',
         'uses' => 'FeesController@archive']
    );
    Route::get('/create',
        ['as' => 'fees.create',
         'uses' => 'FeesController@create',
         'middleware' => 'ajax']
    );
    Route::post('/',
        ['as' => 'fees.store',
         'uses' => 'FeesController@store',
         'middleware' => 'ajax']
    );
    Route::delete('/{id}',
        ['as' => 'fees.destroy',
         'uses' => 'FeesController@destroy',
         'middleware' => 'ajax']
    );

});

/**
 * Meetings management
 *
 * @todo Regular members should be able to view limited details
 */

Route::group(['prefix' => 'meetings', 'middleware' => 'auth|boardMember'], function () {

    Route::get('/',          ['as' => 'meetings.index',   'uses' => 'MeetingsController@index']);
    Route::get('/create',    ['as' => 'meetings.create',  'uses' => 'MeetingsController@create']);
    Route::post('/',         ['as' => 'meetings.store',   'uses' => 'MeetingsController@store']);
    Route::get('/{id}',      ['as' => 'meetings.show',    'uses' => 'MeetingsController@show']);
    Route::get('/{id}/edit', ['as' => 'meetings.edit',    'uses' => 'MeetingsController@edit']);
    Route::put('/{id}',      ['as' => 'meetings.update',  'uses' => 'MeetingsController@update']);
    Route::delete('/{id}',   ['as' => 'meetings.destroy', 'uses' => 'MeetingsController@destroy']);

});
