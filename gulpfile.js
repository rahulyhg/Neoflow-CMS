var gulp = require('gulp'),
        zip = require('gulp-zip'),
        run = require('gulp-run'),
        runSequence = require('run-sequence'),
        prompt = require('gulp-prompt');

// Get package info
var pjson = require('./package.json');

// Build install package
gulp.task('install:release', function (callback) {
    runSequence('install:pullFromGit', 'install:createZip', callback);
});

// Create zip file for installation
gulp.task('install:createZip', function () {
    return gulp
            .src([
                './**',
                '!./config.php',
                '!./package*',
                '!./gulpfile.js',
                '!./node_modules{,/**}',
                '!./nbproject{,/**}',
                '!./src{,/**}',
                '!./robots.txt',
                '!./sitemap.xml',
                '!./temp/update{,/**}',
                '!./media/modules/wysiwyg/{,/**}',
                '!./themes/*/package*',
                '!./themes/*/node_modules{,/**}',
                '!./themes/*/src{,/**}',
                '!./*.zip'
            ])
            .pipe(zip(pjson.name + '-' + pjson.version + '.zip'))
            .pipe(gulp.dest('./'));
});

// Pull latest files and folders from Git
gulp.task('install:pullFromGit', function () {
    return gulp
            .src('.')
            .pipe(run('git pull', {
                usePowerShell: true,
                verbosity: 0
            }));
});

// Build update package
gulp.task('update:release', function (callback) {
    return runSequence('update:checkoutFromGit', 'update:createZip', callback);
});

// Create zip file for update
gulp.task('update:createZip', function () {
    return gulp
            .src([
                './temp/update/**',
                '!./temp/update/install/config.php',
                '!./temp/update/install/package*',
                '!./temp/update/install/gulpfile.js',
                '!./temp/update/install/node_modules{,/**}',
                '!./temp/update/install/install{,/**}',
                '!./temp/update/install/nbproject{,/**}',
                '!./temp/update/install/src{,/**}',
                '!./temp/update/install/robots.txt',
                '!./temp/update/install/sitemap.xml',
                '!./temp/update/install/temp/update{,/**}',
                '!./temp/update/install/media/modules/wysiwyg/{,/**}',
                '!./temp/update/install/themes/*/package*',
                '!./temp/update/install/themes/*/node_modules{,/**}',
                '!./temp/update/install/themes/*/src{,/**}',
                '!./temp/update/*.zip'
            ])
            .pipe(zip(pjson.name + '-' + pjson.version + '-update.zip'))
            .pipe(gulp.dest('./temp/update/'));
});

// Checkout files and folders from Git based on last commit or tag
gulp.task('update:checkoutFromGit', function () {
    var tag = '';
    return gulp
            .src('./temp/update/install')
            .pipe(prompt.prompt({
                type: 'input',
                name: 'tag',
                message: 'From which last commit or tag would you like to checkout?'
            }, function (res) {
                tag = res.tag;
            }))
            .pipe(run('git checkout-index -f --prefix="<%= file.path %>/" $(git diff --name-only ' + tag + ')', {
                usePowerShell: true,
                verbosity: 0
            }));
});