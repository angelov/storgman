## EESTEC Platform for Local Committees

“EESTEC Platform” is a web application which aims to ease the internal management of the EESTEC's local committees.
The main idea for this to be achieved is to automate many of the tasks currently done “by hand” and to collect the big
number of information (such as list of members and their info) currently spread across many spreadsheets, papers, mails
etc. in one place.

[Electrical Engineering Students’ European Association (EESTEC)](http://eestec.net) is a non-political, non-profit
organization of and for Electrical Engineering and Computer Science (EECS) students of universities, institutes and
technical schools in Europe.

You can read the detailed description of the project [here](http://angelovdejan.me/2014/08/05/introducing-eestec-platform-for-local-committees.html).

We are currently implementing this application at [EESTEC LC Skopje](http://members.eestec-sk.org.mk/). Feel free to try it for your LC as well. You can get the latest release of the application on the [releases](https://github.com/angelov/eestec-platform/releases) page. If you find any bugs or have some ideas, please create an issue [here](https://github.com/angelov/eestec-platform/issues).

Under development by [Angelov Dejan](http://angelovdejan.me).

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/angelov/eestec-platform/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/angelov/eestec-platform/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/angelov/eestec-platform/badges/build.png?b=master)](https://scrutinizer-ci.com/g/angelov/eestec-platform/build-status/master)

### Requirements

The application is based on the [Laravel 5](http://laravel.com) framework. To install it you need the following:

* PHP >= 5.4
* MCrypt and GD PHP extensions
* Apache/nginx web server
* Relational database (tested with PostgreSQL and MySQL)
* Composer
* Bower
* Grunt

*Note:* You can use [Laravel Homestead](http://laravel.com/docs/homestead) as your development environment, since it has everything you need for this project.

### Installation

To install the application on your local machine, follow these steps:

1. Download the code from this repository (latest release can be found [here](https://github.com/angelov/eestec-platform/releases))
2. Create new vhost `eestec.local` that points to the `public` directory
3. To configure the application, rename the `.env.example` file to `.env` and update the config values inside
    * You can use `cp .env.example .env` to keep the original file, just in case
    * If you need some more advanced configuration, check the files in the `config` directory
4. Run the following commands from your command line:
    * `composer install` to install the PHP dependencies
    * `bower install` to install the front-end dependencies
    * `npm install` to prepare the project for Grunt
    * `grunt` to start the Grunt tasks
    * `php artisan migrate` to create the database schema
    * `php artisan db:seed` to insert the default data
5. Run `http://eestec.local` in your browser. You should see the login screen.
6. Login using the following credentials (and change them after):
    * email: `admin@ultim8.info`
    * password: `123456`

### Contributing

If you want to contribute, submit an issue or a pull request.

### License

EESTEC Platform for Local Committees    
Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>    
    
This file is part of EESTEC Platform.   
    
ESTEC Platform is free software: you can redistribute it and/or modify  
it under the terms of the GNU General Public License as published by    
the Free Software Foundation, either version 3 of the License, or   
(at your option) any later version. 
    
EESTEC Platform is distributed in the hope that it will be useful,  
but WITHOUT ANY WARRANTY; without even the implied warranty of  
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   
GNU General Public License for more details.    
    
You should have received a copy of the GNU General Public License   
along with EESTEC Platform.  If not, see <http://www.gnu.org/licenses/>.
