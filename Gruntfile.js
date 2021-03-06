// Generated on 2014-04-18 using generator-angular 0.6.0-rc.2
'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

module.exports = function (grunt) {

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // start S3
  // determined this wasnt needed since grunt automatically loads it.
  // this actually causes an error because grunt tries to load it twice
  //grunt.loadNpmTasks('grunt-s3');
  //end S3

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  // Define the configuration for all the tasks
  grunt.initConfig({

    // Project settings
    yeoman: {
      // configurable paths
      app: require('./bower.json').appPath || 'app',
      dist: 'dist'
    },

    // Watches files for changes and runs tasks based on the changed files
    watch: {
      js: {
        files: ['{.tmp,<%= yeoman.app %>}/scripts/{,*/}*.js'],
        tasks: ['newer:jshint:all']
      },
      jsTest: {
        files: ['test/spec/{,*/}*.js'],
        tasks: ['newer:jshint:test', 'karma']
      },
      styles: {
        files: ['<%= yeoman.app %>/styles/{,*/}*.css'],
        tasks: ['newer:copy:styles', 'autoprefixer']
      },
      gruntfile: {
        files: ['Gruntfile.js']
      },
      livereload: {
        options: {
          livereload: '<%= connect.options.livereload %>'
        },
        files: [
          '<%= yeoman.app %>/{,*/}*.html',
          '.tmp/styles/{,*/}*.css',
          '<%= yeoman.app %>/images/{,*/}*.{png,jpg,jpeg,gif,webp,svg}'
        ]
      }
    },

    // The actual grunt server settings
    connect: {
      options: {
        port: 9000,
        // Change this to '0.0.0.0' to access the server from outside.
        hostname: 'localhost',
        livereload: 35729
      },
      livereload: {
        options: {
          open: true,
          base: [
            '.tmp',
            '<%= yeoman.app %>'
          ]
        }
      },
      test: {
        options: {
          port: 9001,
          base: [
            '.tmp',
            'test',
            '<%= yeoman.app %>'
          ]
        }
      },
      dist: {
        options: {
          base: '<%= yeoman.dist %>'
        }
      }
    },

    // Make sure code styles are up to par and there are no obvious mistakes
    jshint: {
      options: {
        jshintrc: '.jshintrc',
        reporter: require('jshint-stylish')
      },
      all: [
        'Gruntfile.js',
        '<%= yeoman.app %>/scripts/{,*/}*.js'
      ],
      test: {
        options: {
          jshintrc: 'test/.jshintrc'
        },
        src: ['test/spec/{,*/}*.js']
      }
    },

    // Empties folders to start fresh
    clean: {
      dist: {
        files: [{
          dot: true,
          src: [
            '.tmp',
            '<%= yeoman.dist %>/*',
            '!<%= yeoman.dist %>/.git*'
          ]
        }]
      },
      server: '.tmp'
    },

    // Add vendor prefixed styles
    autoprefixer: {
      options: {
        browsers: ['last 1 version']
      },
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/styles/',
          src: '{,*/}*.css',
          dest: '.tmp/styles/'
        }]
      }
    },

    // Renames files for browser caching purposes
    rev: {
      dist: {
        files: {
          src: [
            '<%= yeoman.dist %>/scripts/{,*/}*.js',
            '<%= yeoman.dist %>/styles/{,*/}*.css',
            '<%= yeoman.dist %>/images/{,*/}*.{png,jpg,jpeg,gif,webp,svg}',
            '<%= yeoman.dist %>/styles/fonts/*'
          ]
        }
      }
    },

    // Reads HTML for usemin blocks to enable smart builds that automatically
    // concat, minify and revision files. Creates configurations in memory so
    // additional tasks can operate on them
    useminPrepare: {
      html: '<%= yeoman.app %>/index.html',
      options: {
        dest: '<%= yeoman.dist %>'
      }
    },

    // Performs rewrites based on rev and the useminPrepare configuration
    usemin: {
      html: ['<%= yeoman.dist %>/{,*/}*.html'],
      css: ['<%= yeoman.dist %>/styles/{,*/}*.css'],
      options: {
        assetsDirs: ['<%= yeoman.dist %>',
          '<%= yeoman.dist %>/any_name_is_ok'  // Fix relative path issue.
         ]
      }
    },

    // The following *-min tasks produce minified files in the dist folder
    imagemin: {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= yeoman.app %>/images',
          src: '{,*/}*.{png,jpg,jpeg,gif}',
          dest: '<%= yeoman.dist %>/images'
        }]
      }
    },
    svgmin: {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= yeoman.app %>/images',
          src: '{,*/}*.svg',
          dest: '<%= yeoman.dist %>/images'
        }]
      }
    },
    htmlmin: {
      dist: {
        options: {
          // Optional configurations that you can uncomment to use
          // removeCommentsFromCDATA: true,
          // collapseBooleanAttributes: true,
          // removeAttributeQuotes: true,
          // removeRedundantAttributes: true,
          // useShortDoctype: true,
          // removeEmptyAttributes: true,
          // removeOptionalTags: true*/
        },
        files: [{
          expand: true,
          cwd: '<%= yeoman.app %>',
          src: ['*.html', 'views/*.html'],
          dest: '<%= yeoman.dist %>'
        }]
      }
    },

    // Allow the use of non-minsafe AngularJS files. Automatically makes it
    // minsafe compatible so Uglify does not destroy the ng references
    ngmin: {
      dist: {
        files: [{
          expand: true,
          cwd: '.tmp/concat/scripts',
          src: '*.js',
          dest: '.tmp/concat/scripts'
        }]
      }
    },

    // Replace Google CDN references
    cdnify: {
      dist: {
        html: ['<%= yeoman.dist %>/*.html']
      }
    },

    // Copies remaining files to places other tasks can use
    copy: {
      dist: {
        files: [{
          expand: true,
          dot: true,
          cwd: '<%= yeoman.app %>',
          dest: '<%= yeoman.dist %>',
          src: [
            '*.{ico,png,txt}',
            '.htaccess',
            'bower_components/**/*',
            'images/{,*/}*.{webp}',
            'fonts/*'
          ]
        }, {
          expand: true,
          cwd: '.tmp/images',
          dest: '<%= yeoman.dist %>/images',
          src: [
            'generated/*'
          ]
        }]
      },
      styles: {
        expand: true,
        cwd: '<%= yeoman.app %>/styles',
        dest: '.tmp/styles/',
        src: '{,*/}*.css'
      },
      otherfiles: {
        expand: true,
        cwd: '<%= yeoman.app %>',
        dest: '<%= yeoman.dist %>',
        //copy php and sql files from app to dist.. exclude config file
        // exclude DB patch and revert files from base code..
        src: ['*.php', '*.sql','!config.php', '!dbRefreshTables.php', '!DB*.sql']
      },
      bootstrap: {
        expand: true,
        cwd: '<%= yeoman.app %>/styles',
        dest: '<%= yeoman.dist %>/styles',
        src: ['bootstrap.min*.css']
      },
      staticImage: {
        expand: true,
        cwd: '<%= yeoman.app %>/images',
        dest: '<%= yeoman.dist %>/images',
        //only want to copy certain images...
        src: ['desktop_screenshot*.png', 'Giant*.png']
      }
    },

    // Run some tasks in parallel to speed up the build process
    concurrent: {
      server: [
        'copy:styles'
      ],
      test: [
        'copy:styles'
      ],
      dist: [
        'copy:styles',
        'imagemin',
        'svgmin',
        'htmlmin'
      ]
    },

    // Test settings
    karma: {
      unit: {
        configFile: 'karma.conf.js',
        singleRun: true
      }
    },

    // htmlSnapshot settings
    // Doesnt work on Windows but fine on Linux/Mac...
    htmlSnapshot: {
      debug: {
        options: {
          snapshotPath: 'snapshots/',
          fileNamePrefix: 'snapshot_',
          // LinuxMint: sitePath: 'http://localhost',
          // Win7: sitePath: 'http://localhost:8083/#!/',
          // Prod sitePath: 'https://todogiant.com/#!/',
          sitePath: 'https://todogiant.com/#!/',
          msWaitForPages: 6000,
          urls: [
            'contact',
            'demouser',
            'faq',
            'faq_accordian',
            'forgot_password',
            'login',
            'main',
            'register',
            'social',
            'welcome'
          ]
        }
      },
      prod: {
        options: {}
      }
    },
    sitemap: {
      dist: {
        siteRoot: './app/views',
        homepage: 'https://todogiant.com/#!'
      }
    },

    // start of S3
    //LinuxMint?  aws: grunt.file.readJSON('/home/paul-winter/grunt-aws.json'),
    //MAC? aws: grunt.file.readJSON('/Users/pwinter303/grunt-aws.json'),
    //MAC? aws: grunt.file.readJSON('/home/pwinter303/grunt-aws.json'),
    //Win7 aws: grunt.file.readJSON('C:/Users/paul-winter/grunt-aws.json'),
    aws: grunt.file.readJSON('C:/Users/paul-winter/grunt-aws.json'),
    /* following comment turns off camelcase check for this function.. so it'll be ignored */
    /* jshint camelcase: false */
    s3: {
      options: {
        key: '<%= aws.AWSAccessKeyId %>',
        secret: '<%= aws.AWSSecretKey %>',
        bucket: '<%= aws.bucket %>',
        access: 'public-read',
        headers: {
          // Two Year cache policy (1000 * 60 * 60 * 24 * 730)
          'Cache-Control': 'max-age=630720000, public',
          'Expires': new Date(Date.now() + 63072000000).toUTCString()
        }
      },
      sourceCode: {
          // These options override the defaults
          options: {
              // encoding screws up the uploads... instead of uploading to a folder it converts the backslash to 20% (or similar) and dumps it in the root folder
              //encodePaths: true,
              maxOperations: 4
            },
          // Files to be uploaded.
            delete: [
              {src:'source-code/' + grunt.template.today('yyyy-mm-dd')}
            ],
          // Files to be uploaded......
            upload: [
              {src: '<%= yeoman.dist %>/fonts/**/*',
                dest: 'source-code/' + grunt.template.today('yyyy-mm-dd'),
                rel: 'dist'},
              {src: '<%= yeoman.dist %>/scripts/**/*',
                dest: 'source-code/' + grunt.template.today('yyyy-mm-dd'),
                rel: 'dist'},
              {src: '<%= yeoman.dist %>/styles/**/*',
                dest: 'source-code/' + grunt.template.today('yyyy-mm-dd'),
                rel: 'dist'},
              {src: '<%= yeoman.dist %>/views/**/*',
                dest: 'source-code/' + grunt.template.today('yyyy-mm-dd'),
                rel: 'dist'},
              {src: '<%= yeoman.dist %>/*',
                dest: 'source-code/' + grunt.template.today('yyyy-mm-dd'),
                rel: 'dist'},
              {src: '<%= yeoman.dist %>/images/**/*',
                dest: 'source-code/' + grunt.template.today('yyyy-mm-dd'),
                rel: 'dist'}
            ]
          }
        }
    //end of S3


      });


  grunt.registerTask('serve', function (target) {
    if (target === 'dist') {
      return grunt.task.run(['build', 'connect:dist:keepalive']);
    }

    grunt.task.run([
      'clean:server',
      'concurrent:server',
      'autoprefixer',
      'connect:livereload',
      'watch'
    ]);
  });

  grunt.registerTask('server', function () {
    grunt.log.warn('The `server` task has been deprecated. Use `grunt serve` to start a server.');
    grunt.task.run(['serve']);
  });

  grunt.registerTask('test', [
    'clean:server',
    'concurrent:test',
    'autoprefixer',
    'connect:test',
    'karma'
  ]);

  grunt.registerTask('build', [
    'clean:dist',
    'useminPrepare',
    'concurrent:dist',
    'autoprefixer',
    'concat',
    'ngmin',
    'copy:dist',
    'cdnify',
    'cssmin',
    'uglify',
    'rev',
    'usemin'
  ]);

  grunt.registerTask('default', [
    'newer:jshint',
    'test',
    'build',
    'copy:otherfiles',
    'copy:bootstrap',
    'copy:staticImage'
  ]);

};
