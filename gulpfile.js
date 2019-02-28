// Include gulp
var gulp = require('gulp');
// Include plugins
var typescript = require('gulp-typescript');
var sourcemaps = require('gulp-sourcemaps');
var babel = require('gulp-babel');
var rename = require('gulp-rename');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');

//https://gist.github.com/demisx/beef93591edc1521330a
var paths = {
  dirs: {
    build: 'public'
  },
  typescript: 'resources/typescript/*.ts',
  sass: 'resources/scss/*.scss'
};

// Transpile, Concatenate & Minify Typescript
gulp.task('scripts', function () {
    return gulp.src(paths.typescript)
      .pipe(sourcemaps.init())
      .pipe(concat("main.ts"))
      .pipe(typescript({
          noImplicitAny: true,
          outFile: 'main.js'
      }))
      .pipe(babel())
      .pipe(rename({suffix: '.min'}))
      .pipe(uglify())
      .pipe(sourcemaps.write("."))
      .pipe(gulp.dest(paths.dirs.build + '/js'));
});

// Compile and Concatenate CSS
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
