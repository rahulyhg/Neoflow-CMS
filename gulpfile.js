var gulp = require('gulp'),
        zip = require('gulp-zip'),
        run = require('gulp-run'),
        runSequence = require('run-sequence'),
        prompt = require('gulp-prompt'),
        fs = require('fs-extra'),
        path = require('path');
// Get package info
var pjson = require('./package.json');

// Build install package
gulp.task('install:release', function (callback) {
    runSequence('install:_pullFromGit', 'install:_createZipPackage', callback);
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
                '!./temp/installation{,/**}',
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

// Pull latest files and folders from Git
gulp.task('install:_pullFromGit', function () {
    return gulp
            .src('.')
            .pipe(run('git pull', {
                usePowerShell: true,
                verbosity: 0
            }));
});

// Build update package
gulp.task('update:release', function (callback) {
    return runSequence('update:clean', 'update:_getTagForGit', 'update:_checkoutFromGit', 'update:_copyFiles', 'update:_createModuleZipPackages', 'update:_createThemeZipPackages', 'update:_createZipPackage', callback);
});


// Last commit or tag
var tag = '';

// Show prompt to get last commit or tag for checkout
gulp.task('update:_getTagForGit', function () {
    return gulp
            .src('./update/delivery/files')
            .pipe(prompt.prompt({
                type: 'input',
                name: 'tag',
                message: 'From which last commit or tag would you like to checkout?'
            }, function (res) {
                tag = res.tag;
            }));
});

// Checkout files and folders from Git based on last commit or tag
gulp.task('update:_checkoutFromGit', function () {
    return gulp
            .src('./update/delivery/files')
            .pipe(run('git checkout-index -f --prefix="<%= file.path %>/" $(git diff --name-only ' + tag + ')', {
                usePowerShell: true,
                verbosity: 0
            }));
});

// Create zip file for update
gulp.task('update:_createZipPackage', function () {
    console.log('Create ' + pjson.name + '-' + tag + '-to-' + pjson.version + '-update.zip');
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
                '!./update/delivery/files/installation{,/**}',
            ])
            .pipe(zip(pjson.name + '-update-' + tag + '-to-' + pjson.version + '.zip'))
            .pipe(gulp.dest('./'));
});

// Copy index.php, readme and license
gulp.task('update:_copyFiles', function () {
    return gulp
            .src([
                './index.php',
                './LICENSE',
                './README.md',
                '*application/**/*',
                '*vendor/**/*',
                '*statics/**/*'
            ])
            .pipe(gulp.dest('./update/delivery/files'));
});

// Create zip file of each core module (incl. Dummy module)
gulp.task('update:_createModuleZipPackages', function () {

    // Define folder names of core modules
    var moduleFolders = [
        'code', 'datetimepicker', 'dummy', 'sitemap', 'snippets', 'wysiwyg', 'search'
    ];

    // Add folder names of changed modules
    moduleFolders = moduleFolders.concat(fs
            .readdirSync('./update/delivery/files/modules')
            .filter(function (file) {
                return (fs.statSync(path.join('./modules', file)).isDirectory() && moduleFolders.indexOf(file) === -1);
            }));

    // Get all installed module folder names, but only zip core and changed modules
    return fs
            .readdirSync('./modules')
            .filter(function (file) {
                return fs.statSync(path.join('./modules', file)).isDirectory();
            })
            .map(function (moduleFolder) {
                if (moduleFolders.indexOf(moduleFolder) > -1) {
                    console.log('Create ' + moduleFolder + '.zip');
                    return gulp
                            .src(path.join('./modules', moduleFolder) + '/**')
                            .pipe(zip(moduleFolder + '.zip'))
                            .pipe(gulp.dest('./update/delivery/modules'));
                }
                return false;
            });

});


// Create zip file of each core theme
gulp.task('update:_createThemeZipPackages', function () {

    // Define folder names of core themes
    var themeFolders = [
        'neoflow-backend'
    ];

    // Add folder names of changed themes
    themeFolders = themeFolders.concat(fs
            .readdirSync('./update/delivery/files/themes')
            .filter(function (file) {
                return (fs.statSync(path.join('./themes', file)).isDirectory() && themeFolders.indexOf(file) === -1);
            }));

    // Get all installed theme folder names, but only zip core and changed themes
    return fs
            .readdirSync('./themes')
            .filter(function (file) {
                return fs.statSync(path.join('./themes', file)).isDirectory();
            })
            .map(function (themeFolder) {
                if (themeFolders.indexOf(themeFolder) > -1) {
                    console.log('Create ' + themeFolder + '.zip');
                    return gulp
                            .src([
                                path.join('./themes', themeFolder) + '/**',
                                '!./themes/*/package*',
                                '!./themes/*/node_modules{,/**}',
                                '!./themes/*/src{,/**}'
                            ])
                            .pipe(zip(themeFolder + '.zip'))
                            .pipe(gulp.dest('./update/delivery/themes'));
                }
                return false;
            });
});


// Create zip file of each core theme
gulp.task('update:clean', function () {

    // Delete files
    return fs
            .readdirSync('./update/delivery')
            .filter(function (folder) {
                return fs.statSync(path.join('./update/delivery', folder)).isDirectory();
            })
            .map(function (folder) {
                var folderPath = path.join('./update/delivery', folder);
                return fs
                        .readdirSync(folderPath)
                        .map(function (file) {
                            if (file !== '.gitkeep') {
                                fs.removeSync(path.join(folderPath, file));
                            }
                        });
            });
});

