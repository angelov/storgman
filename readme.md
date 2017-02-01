## Storgman - Student Organizations Management

“Storgman” is a web application which aims to ease the internal management of different types of student organizations.
The main idea for this to be achieved is to automate many of the tasks currently done “by hand” and to collect the big
number of information (such as list of members and their info) currently spread across many spreadsheets, papers, mails
etc. in one place.

Under development by [Angelov Dejan](http://angelovdejan.me).

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/angelov/storgman/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/angelov/storgman/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/angelov/storgman/badges/build.png?b=master)](https://scrutinizer-ci.com/g/angelov/storgman/build-status/master)
[![StyleCI](https://styleci.io/repos/22167392/shield)](https://styleci.io/repos/22167392)

### Note

The application was previously called "EESTEC Platform for Local Committees" and was intended to be used by 
 this particular organization. There may be some parts of the application that are still associated with this organizaton.

### Requirements

The application is based on the [Laravel 5](http://laravel.com) framework. To install it you need the following:

* PHP >= 5.6
* MCrypt and GD PHP extensions
* Apache/nginx web server
* Relational database (tested with PostgreSQL and MySQL)
* Composer
* Bower
* Grunt

*Note:* You can use [Laravel Homestead](http://laravel.com/docs/homestead) as your development environment, since it has everything you need for this project.

### Installation

To install the application on your local machine, follow these steps:

1. Download the code from this repository (latest release can be found [here](https://github.com/angelov/storgman/releases))
2. Create new vhost `storgman.local` that points to the `public` directory
3. To configure the application, rename the `.env.example` file to `.env` and update the config values inside
    * You can use `cp .env.example .env` to keep the original file, just in case
    * If you need some more advanced configuration, check the files in the `config` directory
4. Make sure that the `storage` and `bootstrap/cache` folders are writable
5. Run the following commands from your command line:
    * `composer install` to install the PHP dependencies
    * `bower install` to install the front-end dependencies
    * `npm install` to prepare the project for Grunt
    * `grunt` to start the Grunt tasks
    * `php artisan migrate` to create the database schema
    * `php artisan db:seed` to insert the default data
6. Run `http://eestec.local` in your browser. You should see the login screen.
7. Login using the following credentials (and change them after):
    * email: `admin@ultim8.info`
    * password: `123456`

### Contributing

If you want to contribute, submit an issue or a pull request.

### License

Storgman - Student Organizations Management    
Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
    
This file is part of Storgman.   
    
ESTEC Platform is free software: you can redistribute it and/or modify  
it under the terms of the GNU General Public License as published by    
the Free Software Foundation, either version 3 of the License, or   
(at your option) any later version. 
    
Storgman is distributed in the hope that it will be useful,  
but WITHOUT ANY WARRANTY; without even the implied warranty of  
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   
GNU General Public License for more details.    
    
You should have received a copy of the GNU General Public License   
along with Storgman.  If not, see <http://www.gnu.org/licenses/>.
