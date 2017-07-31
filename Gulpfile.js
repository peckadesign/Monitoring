'use strict';

var gulp = require('gulp');

var config = {
	stylesPath : 'www/styles',
	jsPath : 'www/js'
};

var stylesFiles = [
	'vendor/ublaboo/datagrid/assets/dist/datagrid.css',
	'vendor/ublaboo/datagrid/assets/dist/datagrid-spinners.css',
];

gulp.task('styles', function () {
	gulp
		.src(stylesFiles)
		.pipe(gulp.dest(config.stylesPath));
});

var jsFiles = [
	'assets/js/lastrefresh.js',
	'assets/js/main.js',
	'assets/js/nette.ajax.js',
	'vendor/nette/forms/src/assets/netteForms.min.js',
	'vendor/ublaboo/datagrid/assets/dist/datagrid.js',
	'vendor/ublaboo/datagrid/assets/dist/datagrid-instant-url-refresh.js',
	'vendor/ublaboo/datagrid/assets/dist/datagrid-spinners.js',
];

gulp.task('js', function () {
	gulp
		.src(jsFiles)
		.pipe(gulp.dest(config.jsPath));
});

gulp.task('build', ['styles', 'js']);
gulp.task('default', ['build']);
