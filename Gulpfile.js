var gulp = require('gulp');

var config = {
	stylesPath : 'www/styles',
	jsPath : 'www/js'
};

var stylesFiles = [
	'vendor/ublaboo/datagrid/assets/dist/datagrid.css',
	'vendor/ublaboo/datagrid/assets/dist/datagrid-spinners.css'
];

function styles() {
	return gulp
		.src(stylesFiles)
		.pipe(gulp.dest(config.stylesPath));
}

var jsFiles = [
	'assets/js/lastrefresh.js',
	'assets/js/main.js',
	'assets/js/nette.ajax.js',
	'vendor/nette/forms/src/assets/netteForms.min.js',
	'vendor/ublaboo/datagrid/assets/dist/datagrid.js',
	'vendor/ublaboo/datagrid/assets/dist/datagrid-instant-url-refresh.js',
	'vendor/ublaboo/datagrid/assets/dist/datagrid-spinners.js'
];

function js() {
	return gulp
		.src(jsFiles)
		.pipe(gulp.dest(config.jsPath));
}

var build = gulp.series(gulp.parallel(js, styles));

gulp.task('default', build);
