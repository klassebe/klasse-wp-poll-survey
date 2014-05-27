module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      scripts: {
        files: ['**/*.hbs','./js/admin.js'],
        tasks: ['handlebars','clean:dist','concat:dist','uglify','clean:temp'],
        options: {
          spawn: false,
          livereload: true,
        },
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
          "./js/temp/templates.js": "./js/hbs_templates/*.hbs",
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
      dist: {
        src: ['./js/bower_components/handlebars/handlebars.runtime.js', './js/temp/templates.js', './js/handlebars.helpers.js', './js/admin.js' ],
        dest: './js/dist/kwps_admin.js'
      }
    },
    clean: {
      dist: ["./js/dist"],
      temp: ["./js/temp"]
    },
    jshint: {
      all: ['./js/*.js']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-handlebars');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');

  // Default task(s).
  grunt.registerTask('default', ['watch']);
  grunt.registerTask('build', ['jshint', 'handlebars','clean:dist','concat:dist','uglify','clean:temp']);

};