// Include gulp
var gulp = require('gulp');
// Include plugins
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sass = require('gulp-sass');

//https://gist.github.com/demisx/beef93591edc1521330a
var paths = {
  dirs: {
    build: 'public'
  },
  js: 'resources/js/*.js',
  sass: 'resources/scss/*.scss'
};

// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src(paths.js)
      .pipe(concat('main.js'))
      .pipe(rename({suffix: '.min'}))
      .pipe(uglify())
      .pipe(gulp.dest(paths.dirs.build + '/js'));
});

/// Compile and Concatenate CSS
sass.compiler = require('node-sass');

gulp.task('sass', function () {
  return gulp.src(paths.sass)
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest(paths.dirs.build +'/css'));
});

//Watches
gulp.task('watch:styles', function () {
  gulp.watch(paths.sass, gulp.series('sass'));
});

gulp.task('watch', gulp.parallel('watch:styles'));

// Default Task (https://fettblog.eu/gulp-4-parallel-and-series/)
gulp.task('default', gulp.series('scripts', 'sass', 'watch'));

gulp.task('vagrant', gulp.series('scripts', 'sass'));
