var gulp = require('gulp'),
        zip = require('gulp-zip'),
        run = require('gulp-run'),
        runSequence = require('run-sequence'),
        fs = require('fs'),
        path = require('path'),
        flatmap = require('gulp-flatmap'),
        del = require('del');

// Get package info
var pjson = require('./package.json');

// Build install package
gulp.task('install:release', function (callback) {
    runSequence('install:clean', 'source:pullFromGit', 'install:_createModuleZipPackages', 'install:_createThemeZipPackages', 'install:_createZipPackage', callback);
});

// Create zip file for installation
gulp.task('install:_createZipPackage', function () {
    console.log('Create ' + pjson.name + '-' + pjson.version + '.zip');
    return gulp
            .src([
                './**',
                './.htaccess',
                '!./config.php',
                '!./package*',
                '!./composer*',
                '!./**/composer*',
                '!./gulpfile.js',
                '!./node_modules{,/**}',
                '!./nbproject{,/**}',
                '!./robots.txt',
                '!./sitemap.xml',
                '!./logs/*',
                '!./vendor/bin/*',
                '!./modules/**/*',
                '!./update{,/**}',
                '!./media/modules/wysiwyg/{,/**}',
                '!./themes/*/package*',
                '!./themes/*/node_modules{,/**}',
                '!./themes/*/src{,/**}',
                '!./*.zip'
            ])
            .pipe(zip(pjson.name + '-' + pjson.version + '.zip'))
            .pipe(gulp.dest('./'));
});

// Create zip file of each core theme
gulp.task('install:clean', function () {
    return del([
        './installation/modules/**/*',
        '!./installation/modules/.gitkeep',
        './installation/themes/**/*',
        '!./installation/themes/.gitkeep'
    ]);
});

// Create zip packages of each module
gulp.task('install:_createModuleZipPackages', function () {
    return gulp
            .src([
                './modules/*',
                '!./modules/dummy'
            ])
            .pipe(flatmap(function (stream, file) {
                if (fs.statSync(file.path).isDirectory()) {
                    console.log('Create ' + path.basename(file.path) + '.zip');
                    return gulp
                            .src(file.path + '/**')
                            .pipe(zip(path.basename(file.path) + '.zip'));
                }
            }))
            .pipe(gulp.dest('./installation/modules'));
});

// Create zip packages of each theme
gulp.task('install:_createThemeZipPackages', function () {
    return gulp
            .src([
                './themes/*',
                '!./themes/neoflow-backend'
            ])
            .pipe(flatmap(function (stream, file) {
                if (fs.statSync(file.path).isDirectory()) {
                    console.log('Create ' + path.basename(file.path) + '.zip');
                    return gulp
                            .src([
                                file.path + '/**',
                                '!./themes/*/package*',
                                '!./themes/*/node_modules{,/**}',
                                '!./themes/*/src{,/**}'
                            ])
                            .pipe(zip(path.basename(file.path) + '.zip'));
                }
            }))
            .pipe(gulp.dest('./installation/themes'));
});

// Build update package
gulp.task('update:release', function (callback) {
    return runSequence('update:clean', 'source:pullFromGit', 'update:_copyFiles', 'update:_createModuleZipPackages', 'update:_createThemeZipPackages', 'update:_createZipPackage', callback);
});

// Create zip file for update
gulp.task('update:_createZipPackage', function () {
    console.log('Create ' + pjson.name + '-' + pjson.prior_version + '-to-' + pjson.version + '-update.zip');
    return gulp
            .src([
                './update/**',
                '!./update/delivery/files/config.php',
                '!./update/delivery/files/package*',
                '!./update/delivery/files/composer*',
                '!./update/delivery/files/gulpfile.js',
                '!./update/delivery/files/node_modules{,/**}',
                '!./update/delivery/files/installation{,/**}',
                '!./update/delivery/files/nbproject{,/**}',
                '!./update/delivery/files/src{,/**}',
                '!./update/delivery/files/modules{,/**}',
                '!./update/delivery/files/robots.txt',
                '!./update/delivery/files/sitemap.xml',
                '!./update/delivery/files/temp{,/**}',
                '!./update/delivery/files/media/modules/wysiwyg{,/**}',
                '!./update/delivery/files/themes{,/**}',
                '!./update/delivery/files/installation{,/**}'
            ])
            .pipe(zip(pjson.name + '-update-' + pjson.prior_version + '-to-' + pjson.version + '.zip'))
            .pipe(gulp.dest('./'));
});

// Copy index.php, readme and license
gulp.task('update:_copyFiles', function () {
    return gulp
            .src([
                './index.php',
                './autoload.php',
                './LICENSE',
                './README.md',
                '*application/**/*',
                '*vendor/**/*',
                '*statics/**/*'
            ])
            .pipe(gulp.dest('./update/delivery/files'));
});


// Create zip packages of each module
gulp.task('update:_createModuleZipPackages', function () {
    return gulp
            .src([
                './modules/*',
                '!./modules/dummy'
            ])
            .pipe(flatmap(function (stream, file) {
                if (fs.statSync(file.path).isDirectory()) {
                    console.log('Create ' + path.basename(file.path) + '.zip');
                    return gulp
                            .src(file.path + '/**')
                            .pipe(zip(path.basename(file.path) + '.zip'));
                }
            }))
            .pipe(gulp.dest('./update/delivery/modules'));
});


// Create zip packages of each theme
gulp.task('update:_createThemeZipPackages', function () {
    return gulp
            .src('./themes/*')
            .pipe(flatmap(function (stream, file) {
                if (fs.statSync(file.path).isDirectory()) {
                    console.log('Create ' + path.basename(file.path) + '.zip');
                    return gulp
                            .src([
                                file.path + '/**',
                                '!./themes/*/package*',
                                '!./themes/*/node_modules{,/**}',
                                '!./themes/*/src{,/**}'
                            ])
                            .pipe(zip(path.basename(file.path) + '.zip'));
                }
            }))
            .pipe(gulp.dest('./update/delivery/themes'));
});


// Create zip file of each core theme
gulp.task('update:clean', function () {
    return del([
        './update/delivery/files/**/*',
        './update/delivery/modules/**/*',
        './update/delivery/themes/**/*',
        '!./update/delivery/files/.gitkeep',
        '!./update/delivery/modules/.gitkeep',
        '!./update/delivery/themes/.gitkeep',
    ]);
});


// Source clean
gulp.task('source:clean', function (callback) {
    runSequence('install:clean', 'update:clean', callback);
});

// Pull latest files and folders from Git
gulp.task('source:pullFromGit', function () {
    return gulp
            .src('.')
            .pipe(run('git pull', {
                usePowerShell: true,
                verbosity: 0
            }));
});