## EESTEC Platform for Local Committees

Under development by [Angelov Dejan](http://ultim8.info).   
The application is not production ready yet!

### Requirements

* PHP >= 5.4
* MCrypt and GD PHP extensions
* Apache/nginx web server
* Relational database (tested with MySQL and PostgreSQL)
* Composer
* Bower
* Grunt

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
