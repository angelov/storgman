var dir = 'public/static/assets';

module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            dist: {
                src: [
                    dir + '/vendor/jquery/dist/jquery.js',
                    dir + '/vendor/bootstrap/dist/js/bootstrap.js',
                    dir + '/vendor/holderjs/holder.js',
                    dir + '/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js',
                    dir + '/vendor/highcharts-release/highcharts.js',
                    dir + '/vendor/handlebars/handlebars.min.js',
                    dir + '/vendor/handlebars/handlebars.runtime.min.js',
                    dir + '/vendor/wysihtml5x/dist/wysihtml5x-toolbar.min.js',
                    dir + '/vendor/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.min.js',
                    dir + '/vendor/typeahead.js/dist/typeahead.jquery.min.js',
                    dir + '/vendor/typeahead.js/dist/bloodhound.min.js',
                    dir + '/vendor/moment/moment.js',
                    dir + '/app.js',
                    dir + '/charts.js'
                ],
                dest: dir + '/build/js/app.js'
            }
        },
        uglify: {
            options: {
                mangle: true
            },
            build: {
                src: 'public/static/assets/build/js/app.js',
                dest: 'public/static/assets/build/js/app.js'
            }
        },
        cssmin: {
            minify: {
                files: {
                    'public/static/assets/build/css/style.css': [
                        dir + '/vendor/bootstrap/dist/css/bootstrap.css',
                        dir + '/vendor/bootstrap-datepicker/css/datepicker3.css',
                        dir + '/vendor/font-awesome/css/font-awesome.css',
                        dir + '/vendor/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.css',
                        dir + '/style.css',
                        dir + '/typeahead-fix.css'
                    ]
                }
            }
        },
        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        cwd: dir + "/vendor/bootstrap/dist/fonts/",
                        src: ['**'],
                        dest: dir + '/build/fonts/'
                    },
                    {
                        expand: true,
                        cwd: dir + "/vendor/font-awesome/fonts/",
                        src: ['**'],
                        dest: dir + '/build/fonts/'
                    }
                ]
            }
        }

    });

    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default', ['concat', 'uglify', 'cssmin', 'copy']);

};