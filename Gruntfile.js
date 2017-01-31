module.exports = function(grunt) {

	grunt.initConfig({
		copy: {
			main: {
				src: [
					'assets/**',
					'!assets/*/src/**',
					'!assets/*/src',
					'includes/**',
					'languages/**',
					'CHANGELOG.md',
					'composer.json',
					'LICENSE.md',
					'readme.txt',
					'schemify.php'
				],
				dest: 'dist/'
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: 'languages/',
					mainFile: 'schemify.php',
					type: 'wp-plugin',
					updateTimestamp: false,
					updatePoFiles: true
				}
			}
	}
	});

	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.registerTask('i18n', ['makepot']);
	grunt.registerTask('build', ['i18n', 'copy']);
};
