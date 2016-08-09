module.exports = function(grunt) {

	grunt.initConfig({
		copy: {
			main: {
				src: [
					'assets/**',
					'!assets/*/src/**',
					'!assets/*/src',
					'inc/**',
					'languages/**',
					'CHANGELOG.md',
					'schemify.php',
					'LICENSE.md',
					'readme.txt'
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
