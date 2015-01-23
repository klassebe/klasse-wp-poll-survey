module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			admin_results: {
				src : ['./js/admin-results.js', './js/bower_components/highcharts-release/highcharts.js'],
				dest : './assets/js/kwps_admin-results.js'
			},
			admin_versions: {
				src : ['./js/version-handling.js', './js/bower_components/jquery.are-you-sure/jquery.are-you-sure.js'],
				dest : './assets/js/kwps_admin-versions.js'
			},
			public: {
				src : ['./js/public.js', './js/bower_components/highcharts-release/highcharts.js'],
				dest : './assets/js/kwps_public.js'
			}
		},
		jshint : {
			src : ['./js/*.js'],
			options : {
				ignores : './js/bower_components'
			}
		},
		uglify : {
			my_target : {
				files : [{
					expand : true,
					cwd : './assets/js',
					src : '*.js',
					dest : './assets/js/',
					ext : '.js'
				}]
			}
		},
		clean : {
			scripts : './assets/js',
			css : './assets/css',
			images : './assets/images'
		},
		watch : {
			scripts : {
				files : './js/*.js',
				tasks : ['jshint', 'clean:scripts', 'concat']
			},
			css : {
				files : './css/**',
				tasks : ['clean:css', 'less']
			},
			images : {
				files : './images/**',
				tasks : ['clean:images', 'copy:images']
			}
		},
		copy : {
			images : {
				src : './images/**',
				dest : './assets/'
			}
		},
		less: {
			admin: {
				src : './css/admin.less',
				dest : './assets/css/kwps_admin.css'
			},
			public : {
				src : './css/public.less',
				dest : './assets/css/kwps_public.css'
			}
		},
		githooks : {
			all : {
				'pre-commit' : 'jshint'
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-githooks');


	// Default task(s).
	grunt.registerTask('default', ['jshint', 'clean', 'concat', 'copy:images', 'less', 'watch']);
	grunt.registerTask('build', ['jshint', 'clean', 'concat', 'copy:images', 'less', 'uglify']);
};
