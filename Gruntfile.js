'use strict';
module.exports = function(grunt) {

	grunt.initConfig({
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'!webroot/js/tmt.min.js'
			]
		},
		less: {
			dist: {
				files: {
					'webroot/css/vendor.min.css': [
						'webroot/vendor/bower_components/bootstrap/dist/css/bootstrap.css',
						'webroot/vendor/bower_components/font-awesome/css/font-awesome.min.css'
					],
					'webroot/css/tmt.min.css': [
						'webroot/css/less/styles.less'
					]
				},
				options: {
					compress: true
				}
			}
		},
		uglify: {
			dist: {
				files: {
					'webroot/js/tmt.min.js': [
						'webroot/js/tmt.js'
					],
					'webroot/js/vendor.min.js': [
						'webroot/vendor/bower_components/jquery/dist/jquery.min.js',
						'webroot/vendor/bower_components/bootstrap/dist/js/bootstrap.min.js',
						'webroot/vendor/bower_components/typeahead.js/dist/typeahead.bundle.min.js',
						'webroot/vendor/bower_components/d3/d3.min.js'
					]
				},
				options: {
					//compress: true,
					//beautify: false,
					//mangle: true
				}
			}
		},
		watch: {
			less: {
				files: [
					'webroot/css/less/*.less',
				],
				tasks: ['less']
			}/*,
			js: {
				files: [
					'<%= jshint.all %>',
					'webroot/js/tmt.js'
				],
				tasks: ['jshint', 'uglify']
			}*/
		},
		clean: {
			dist: [
				'assets/css/tmt.min.css',
				'assets/js/tmt.min.js'
			]
		}
	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-less');

	// Register tasks
	grunt.registerTask('default', [
		'clean',
		'less',
		'uglify'
	]);

	grunt.registerTask('dev', [
		'watch'
	]);

};
