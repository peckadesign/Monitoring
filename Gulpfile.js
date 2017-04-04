'use strict';

var gulp = require('gulp');
var gutil = require('gulp-util');
var plugins = require('gulp-load-plugins')();
var bower = require('gulp-bower');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');

var config = {
	assetsPath : 'assets',
	cssPath : 'www/css',
	jsPath : 'www/js',
	bowerPath : 'temp/bower'
};

var jsFiles = [
	config.bowerPath + '/stomp-websocket/lib/stomp.js',
	config.assetsPath + '/js/main.js',
	config.assetsPath + '/js/nette.ajax.js',
	config.assetsPath + '/js/stomp.js',
	config.assetsPath + '/js/websocket.js'
];

gulp.task('bower', function () {
	return bower('temp/bower');
});

gulp.task('js', ['bower'], function () {
	gulp
		.src(jsFiles)
		.pipe(gulp.dest(config.jsPath));
});


// Úlohy

gulp.task('watch', function () {
});


gulp.task('build', ['js']);
gulp.task('default', ['build']);
