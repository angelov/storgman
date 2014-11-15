## EESTEC Platform for Local Committees

“EESTEC Platform” is a web application which aims to ease the internal management of the EESTEC's local committees.
The main idea for this to be achieved is to automate many of the tasks currently done “by hand” and to collect the big
number of information (such as list of members and their info) currently spread across many spreadsheets, papers, mails
etc. in one place.

[Electrical Engineering Students’ European Association (EESTEC)](http://eestec.net) is a non-political, non-profit
organization ofand for Electrical Engineering and Computer Science (EECS) students of universities, institutes and
technical schools in Europe.

You can read the detailed description of the project [here](http://angelovdejan.wordpress.com/2014/08/05/introducing-eestec-platform-for-local-committees/).

Under development by [Angelov Dejan](http://ultim8.info).
The application is not production ready yet!

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/angelov/eestec-platform/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/angelov/eestec-platform/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/angelov/eestec-platform/badges/build.png?b=master)](https://scrutinizer-ci.com/g/angelov/eestec-platform/build-status/master)

### Roadmap

##### Version 1 
I'm a student and I work full time on some other stuff, so, unfortunately, I can't provide the full attention this project needs. Because of that, I can't tell a concrete release date of the application. However, the first version is almost finished and only some smaller pieces are missing for it to be completed.

Here is a list of some planned things that still need to be finished:
* Editing meeting reports
* The results from some dabatabase queries can be cached
* Sending emails to the members when some events occur (eg. their account is approved)
* Members should be able to edit their profiles
* The configuration files can be cleaned from some unnecessary stuff
* Provide option to store the static files (like members' photos) on AWS (or something similar)

I also plan to upgrade this application to use Laravel 5 Framework (scheduled to be released in late november/early december) before releasing the first version.

##### Version 2

Here's a list of some features planned to be included in the second version:

* Option for the members to connect their accounts with their other accounts on some social networks (eg. to be able to login using their Facebook accounts)
* Office space reservations - Members can check if the office space is available and request to use it for some meeting or something similar.
* ....

### Requirements

* PHP >= 5.4
* MCrypt and GD PHP extensions
* Apache/nginx web server
* Relational database (tested with PostgreSQL and MySQL)
* Composer
* Bower
* Grunt

Note: You can use [Laravel Homestead](http://laravel.com/docs/homestead) as your development environment, since it has everything you need for this project.

### Installation

To install the application on your local machine, follow these steps:

1. Download the code from this repository
2. Create new vhost `eestec.local` that points to the `public` dir
3. Edit the needed files in `app/config` to configure the application
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

If you want to contribute, submit an issue or pull request.

### License

EESTEC Platform for Local Committees    
Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>    
    
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
