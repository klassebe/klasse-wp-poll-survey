module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      scripts: {
        files: ['**/*.hbs','./js/*.js', './css/*.css'],
        tasks: ['jshint', 'handlebars','clean:assets','concat:admin','clean:temp', 'concat:public', 'copy:tinymce'],
        options: {
          spawn: false,
          livereload: true
        }
      }
    },
    handlebars: {
      compile: {
        options: {
          namespace: "kwps_admin_templates",
          processName: function(filePath) {
            var pieces = filePath.split("/");
            pieces = pieces[pieces.length - 1];
            return pieces.split('.')[0];
          }
        },
        files: {
          "./js/temp/templates.js": "./js/hbs_templates/*.hbs"
        }
      }
    },
    uglify: {
      my_target: {
        files: {
          './js/dist/kwps_admin.min.js': ['./js/dist/kwps_admin.js']
        }
      }
    },
    concat: {
      admin: {
        files : {
          'assets/js/kwps_admin.js' : [
            './js/bower_components/backbone.validation/dist/backbone-validation.js', 
            './js/bower_components/handlebars/handlebars.runtime.js',
            './js/temp/templates.js', 
            './js/handlebars.helpers.js', 
            './js/admin.js' 
          ],
          'assets/css/kwps_admin.css' : [
            './js/bower_components/jquery-ui/themes/base/minified/jquery.ui.core.min.css',
            './js/bower_components/jquery-ui/themes/base/minified/jquery.ui.tabs.min.css',
            './css/admin.css',
            './css/editor.css'
          ]
        },
        options : {
          banner: "/* kwps admin */ \n\r"
        }
      },
      public: {
        files : {
          'assets/js/kwps_public.js' : [
            './js/public.js',
            './js/bower_components/highcharts-release/highcharts.js'
          ],
          'assets/css/kwps_public.css' : [
            './css/public.css'
          ]
        }
      }
    },
    clean: {
      assets: ["./assets"],
      temp: ["./js/temp"],
      deploy: ['deploy','temp']
    },
    jshint: {
      all: {
        options: {
          jshintrc: '.jshintrc' // relative to Gruntfile
        },
        src: './js/*.js'
      }
    },
    preprocess : {
      options: {
        context : {
          DEBUG: false
        }
      },
      deploy: {
        files : {
          './deploy/views/add.php': 'views/add.php'
        },
      }
    },
    copy :  {
      tinymce: {
        files: [
          {expand: true, cwd : './js/bower_components/', src: ['tinymce/**'], dest: 'assets/lib/'}
        ]
      },
      deploy : {
        files: [
          {src: ['includes/**'], dest: 'deploy/'},
          {src: ['languages/**'], dest: 'deploy/'},
          {src: ['testmodi/**'], dest: 'deploy/'},
          {src: ['assets/**'], dest: 'deploy/'},
          {src: ['index.php', 'klasse-wp-poll-survey.php', 'LICENCE', 'LICENCE.txt', 'README.md', 'README.txt', 'uninstall.php' ], dest: 'deploy/'},
          {src: ['views/**'], dest: 'deploy/'},
        ]
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-handlebars');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-preprocess');


  // Default task(s).
  grunt.registerTask('default', ['dev','watch']);
  grunt.registerTask('dev',['jshint', 'handlebars','clean:assets','concat:admin','clean:temp', 'concat:public', 'copy:tinymce'])
  grunt.registerTask('build', ['dev','uglify']);
  grunt.registerTask('deploy', ['build','clean:deploy','copy:deploy','preprocess:deploy']);
};