// 载入外挂
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    minifycss = require('gulp-minify-css'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    imagemin = require('gulp-imagemin'),
    cleanHtml  = require("gulp-cleanhtml"),
    clean = require('gulp-clean'),
    notify = require('gulp-notify'),
    cache = require('gulp-cache'),
    copy = require('gulp-contrib-copy'),
    livereload = require('gulp-livereload'),
    webpack = require('gulp-webpack');

//定义源代码的目录和编译压缩后的目录
var src='tpl_src',
    dist='tpl';

// 样式
gulp.task('styles', function() {
	return gulp.src(src+'/**/*.scss')
    .pipe(sass())
    .pipe(minifycss())
    .pipe(gulp.dest(dist))
//    .pipe(notify({ message: 'styles task complete' }));
});

// 脚本
gulp.task('scripts', function(callback) {
  return gulp.src(src+'/entry.js')
      .pipe(webpack( require('./webpack.config.js') ))
      .pipe(gulp.dest(dist))
});

// 图片
gulp.task('images', function() {
  return gulp.src([src+'/**/*.+(jpg|jpeg|png|gif|bmp)'])
      .pipe(cache(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true })))
      .pipe(gulp.dest(dist))
//      .pipe(notify({ message: 'Images task complete' }));
});
//html
gulp.task('html', function() {
  gulp.src(src+'/**/*.html')
  	  .pipe(cleanHtml())
      .pipe(gulp.dest(dist))
//      .pipe(notify({ message: 'html task complete' }));
});

//其他不编译的文件直接copy
gulp.task('copy', function () {
    gulp.src(src+'/**/*.!(jpg|jpeg|png|gif|bmp|scss|js|html)')
    .pipe(copy())
    .pipe(gulp.dest(dist));
});

// 清理
gulp.task('clean', function() {
  return gulp.src([dist], {read: false})
      .pipe(clean());
});

// 预设任务
gulp.task('default', ['clean'], function() {
  gulp.start('styles', 'scripts', 'images', 'html');
});

gulp.task('watch', function() {

  // 看守所有.scss档
  gulp.watch(src+'/**/*.scss', ['styles']);

  // 看守所有.js档
  gulp.watch(src+"/**/*.js", ['scripts']);

  // 看守所有图片档
  gulp.watch(src+'/**/*.+(jpg|jpeg|png|gif|bmp)', ['images']);

  //看守html
  gulp.watch(src+"/**/*.html", ['html']);
  
  //监听其他不编译的文件 有变化直接copy
  gulp.watch(src+'/**/*.!(jpg|jpeg|png|gif|bmp|scss|js|html)', ['copy']);

  livereload.listen();
  gulp.watch(dist+'/**').on('change', livereload.changed);

});