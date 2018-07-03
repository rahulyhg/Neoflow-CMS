var gulp = require('gulp');
var fs = require('fs');
var sass = require('gulp-sass');
var rename = require('gulp-rename');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify-es').default;
var replace = require('gulp-replace');
var util = require('gulp-util');
var injectString = require('gulp-inject-string');
var stripComments = require('gulp-strip-comments');
var stripCssComments = require('gulp-strip-css-comments');
var postcss = require('gulp-postcss');
var runSequence = require('run-sequence');
var pjson = require('./package.json');

var sourceHeader = fs
    .readFileSync('./src/source-header.txt', 'utf8')
    .replace('{VERSION}', pjson.version)
    .replace('{YEAR}', new Date().getFullYear());

gulp.task('scss:build', function () {
    return gulp.src('./src/sass/**/*.scss')
        .pipe(sass({
            outputStyle: 'expanded'
        }).on('error', util.log))
        .pipe(stripCssComments({
            preserve: false
        }))
        .pipe(gulp.dest('./src/css'));
});

gulp.task('css:build', function () {
    return gulp.src(['./src/css/*.css'])
        .pipe(postcss([
            require('autoprefixer')({browsers: ['last 2 version', '> 10%']}),
            require('css-mqpacker')(),
            //require('postcss-font-weights')()
        ]))
        .pipe(replace(/([\r\n]{2,})/igm, '\r\n'))
        .pipe(injectString.after('@charset "UTF-8";', '\n' + sourceHeader))
        .pipe(gulp.dest('./css'));
});

gulp.task('css:minify', function () {
    return gulp.src(['./css/*.css', '!./css/*.min.css'])
        .pipe(postcss([
            require('cssnano')({
                safe: true,
                sourcemap: true
            })
        ]))
        .pipe(injectString.after('@charset "UTF-8";', '\n' + sourceHeader + '\n'))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./css'));
});

gulp.task('js:build', function () {
    return gulp.src([
        './src/js/vendor/jquery.min.js',
        './src/js/vendor/popper.min.js',
        './src/js/vendor/bootstrap.min.js',
        './src/js/vendor/bootstrap-fileselect.min.js',
        './src/js/vendor/jquery.easing.min.js',
        './src/js/vendor/jquery.nicescroll.min.js',
        './src/js/vendor/select2.full.min.js',
        './src/js/vendor/nestable.js',
        './src/js/vendor/jquery.mark.min.js',

        './src/js/vendor/DataTables/jquery.dataTables.min.js',
        './src/js/vendor/DataTables/dataTables.bootstrap4.min.js',
        './src/js/vendor/DataTables/dataTables.responsive.min.js',
        './src/js/vendor/DataTables/responsive.bootstrap4.min.js',
        './src/js/vendor/DataTables/datatables.mark.js',
        './src/js/vendor/DataTables/responsive.bootstrap4.min.js',

        './src/js/theme/base.js',
        './src/js/theme/navigation.js',
        './src/js/theme/functions.js',
        './src/js/theme/timer.js',
        './src/js/theme/collapse-history.js',

        './src/js/theme/init/select2.js',
        './src/js/theme/init/fileselect.js',
        './src/js/theme/init/nestable.js',
        './src/js/theme/init/dataTables.js',

        './src/js/theme/modal/relogin.js',
        './src/js/theme/modal/alert.js',
        './src/js/theme/modal/confirm.js',
        './src/js/theme/modal/custom.js'
    ])
        .pipe(stripComments())
        .pipe(concat('script.js'))
        .pipe(injectString.prepend(sourceHeader + '\n'))
        .pipe(gulp.dest('./js'));
});

gulp.task('js:minify', function () {
    return gulp.src(['./js/script.js'])
        .pipe(uglify().on('error', util.log))
        .pipe(rename({suffix: '.min'}))
        .pipe(injectString.prepend(sourceHeader + '\n'))
        .pipe(gulp.dest('./js'));
});

gulp.task('css:rebuild', function () {
    runSequence('scss:build', 'css:build', 'css:minify');
});

gulp.task('js:rebuild', function () {
    runSequence('js:build', 'js:minify');
});

gulp.task('src:rebuild', function () {
    runSequence('js:rebuild', 'css:rebuild');
});

gulp.task('src:watch', function () {
    gulp.watch(['./src/sass/**/*.scss'], function () {
        runSequence('scss:build', 'css:build', 'css:minify');
    });
    gulp.watch(['./src/js/**/*.js'], function () {
        runSequence('js:build', 'js:minify');
    });
});