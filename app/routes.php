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

Route::get('/', ['as' => 'homepage', 'uses' => 'HomeController@showHomepage', 'before' => 'auth']);

/**
 * Authentication
 */

Route::group(['prefix' => 'auth'], function () {

    Route::get('/',
        ['as' => 'auth',
         'uses' => 'AuthController@index',
         'before' => 'guest']
    );
    Route::post('/',
        ['as' => 'postAuth',
         'uses' => 'AuthController@login',
         'before' => 'guest']
    );
    Route::get('/logout',
        ['as' => 'logout',
         'uses' => 'AuthController@logout',
         'before' => 'auth']
    );

});

/**
 * Members management
 */

Route::group(['prefix' => 'members'], function () {

    Route::group(['before' => 'guest'], function() {
        Route::get('/register',
            ['as' => 'members.register',
             'uses' => 'MembersController@register']
        );
        Route::post('/register',
            ['as' => 'members.postRegister',
             'uses' => 'MembersController@postRegister']
        );
    });

    Route::group(['before' => 'auth|boardMember'], function() {
        Route::get('/',          ['as' => 'members.index',  'uses' => 'MembersController@index']);
        Route::get('/create',    ['as' => 'members.create', 'uses' => 'MembersController@create']);
        Route::post('/',         ['as' => 'members.store',  'uses' => 'MembersController@store']);
        Route::get('/{id}',      ['as' => 'members.show',   'uses' => 'MembersController@show']);
        Route::get('/{id}/edit', ['as' => 'members.edit',   'uses' => 'MembersController@edit']);
        Route::put('/{id}',      ['as' => 'members.update', 'uses' => 'MembersController@update']);
        Route::delete('/{id}',
            ['as' => 'members.destroy',
             'uses' => 'MembersController@destroy',
             'before' => 'ajax']
        );
        Route::get('/prefetch',
            ['as' => 'members.prefetch',
             'uses' => 'MembersController@prefetch',
             'before' => 'ajax']
        );
        Route::get('/board',     ['as' => 'members.board',  'uses' => 'MembersController@board']);
        Route::get('/{id}/quick-info',
            ['as' => 'members.quick',
             'uses' => 'MembersController@quickMemberInfo',
             'before' => 'ajax']
        );
        Route::get('/unapproved', ['as' => 'members.unapproved',  'uses' => 'MembersController@unapproved']);
    });

});

/**
 * Membership fees management
 */

Route::group(['prefix' => 'fees', 'before' => 'auth|boardMember'], function () {

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
         'before' => 'ajax']
    );
    Route::post('/',
        ['as' => 'fees.store',
         'uses' => 'FeesController@store',
         'before' => 'ajax']
    );
    Route::delete('/{id}',
        ['as' => 'fees.destroy',
         'uses' => 'FeesController@destroy',
         'before' => 'ajax']
    );

});

/**
 * Meetings management
 *
 * @todo Some routes should be allowed only to board members
 */

Route::group(['prefix' => 'meetings', 'before' => 'auth'], function () {

    Route::get('/',          ['as' => 'meetings.index',   'uses' => 'MeetingsController@index']);
    Route::get('/create',    ['as' => 'meetings.create',  'uses' => 'MeetingsController@create']);
    Route::post('/',         ['as' => 'meetings.store',   'uses' => 'MeetingsController@store']);
    Route::get('/{id}',      ['as' => 'meetings.show',    'uses' => 'MeetingsController@show']);
    Route::get('/{id}/edit', ['as' => 'meetings.edit',    'uses' => 'MeetingsController@edit']);
    Route::put('/{id}',      ['as' => 'meetings.update',  'uses' => 'MeetingsController@update']);
    Route::delete('/{id}',   ['as' => 'meetings.destroy', 'uses' => 'MeetingsController@destroy']);

});
