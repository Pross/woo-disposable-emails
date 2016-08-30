module.exports = function(grunt) {

		require('load-grunt-tasks')(grunt);

		var pkg		 = grunt.file.readJSON('package.json');
		var slug		= 'woo-disposable-emails';
		pkg.version = grunt.file.read( slug + '.php' )
										.match(/(Version):\s+?([0-9\.-]+)/g)[0]
										.replace(/Version:\s+?/, '')
										.trim()

		grunt.initConfig({

			pkg: pkg,

			clean: [
				"dist/*",
				"dist"
			],
			copy: {
				build: {
					files: [
						// includes files within path
						{
							expand:	 true,
							src:			[ '**', pkg.copyIgnores ],
							dest:		 'dist/',
							filter:	 'isFile'
						}
					]
				}
			},

			wp_deploy: {
				deploy: {
					options: {
						plugin_slug: slug,
						svn_user: 'pross',
						svn_url: 'https://plugins.svn.wordpress.org/woo-disposable-emails/',
						build_dir: 'dist', //relative path to your build directory
						assets_dir: 'wp-assets', //relative path to your assets directory (optional).
						max_buffer: 1024*1024
					},
				}
			},
			replace: {
				readme: {
					src: ['dist/readme.txt'],
					overwrite: true,
					replacements: [{
						from: '#version#', // string replacement
						to: pkg.version
					}]
				}
			}
		});

		// task to reload the package data after versions have changed.
		grunt.registerTask('readpkg', 'Read in the package.json file', function() {
			grunt.config.set('pkg', grunt.file.readJSON('package.json'));
		});

		// Default grunt task,
		grunt.registerTask( 'default', [ 'clean' ] );

		grunt.registerTask( 'deploy', [
			'clean',
			'copy',
			'replace',
			'wp_deploy'
		] )

}
