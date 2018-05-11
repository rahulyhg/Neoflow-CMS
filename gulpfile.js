var gulp = require('gulp'),
        zip = require('gulp-zip'),
        run = require('gulp-run'),
        runSequence = require('run-sequence'),
        prompt = require('gulp-prompt'),
        fs = require('fs'),
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
    return runSequence('update:_getTagForGit', 'update:_checkoutFromGit', 'update:_copyVendor', 'update:_createModuleZipPackages', 'update:_createThemeZipPackages', 'update:_createZipPackage', callback);
});

// Create zip file for update
gulp.task('update:_createZipPackage', function () {
    console.log('Create ' + pjson.name + '-' + pjson.version + '-update.zip');
    return gulp
            .src([
                './update/**',
                '!./update/install/config.php',
                '!./update/install/package*',
                '!./update/install/composer*',
                '!./update/install/gulpfile.js',
                '!./update/install/node_modules{,/**}',
                '!./update/install/installation{,/**}',
                '!./update/install/nbproject{,/**}',
                '!./update/install/src{,/**}',
                '!./update/install/modules{,/**}',
                '!./update/install/robots.txt',
                '!./update/install/sitemap.xml',
                '!./update/install/temp{,/**}',
                '!./update/install/media/modules/wysiwyg{,/**}',
                '!./update/install/themes{,/**}',
                '!./update/install/installation{,/**}',
                '!./update/*.zip'
            ])
            .pipe(zip(pjson.name + '-update-' + pjson.version + '.zip'))
            .pipe(gulp.dest('./update/'));
});

// Last commit or tag
var tag = '';

// Show prompt to get last commit or tag for checkout
gulp.task('update:_getTagForGit', function () {
    return gulp
            .src('./update/install')
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
            .src('./update/install')
            .pipe(run('git checkout-index -f --prefix="<%= file.path %>/" $(git diff --name-only ' + tag + ')', {
                usePowerShell: true,
                verbosity: 0
            }));
});

// Copy all vendor files and folders to the update
gulp.task('update:_copyVendor', function () {
    return gulp
            .src('./vendor/**/*')
            .pipe(gulp.dest('./update/install/vendor'));
});

// Create zip file of each core module (incl. Dummy module)
gulp.task('update:_createModuleZipPackages', function () {

    // Define folder names of core modules
    var coreModuleFolders = [
        'code', 'datetimepicker', 'dummy', 'sitemap', 'snippets', 'wysiwyg', 'search'
    ];

    // Add folder names of changed modules
    fs.lstat('./update/install/modules', (err, stats) => {
        if (stats && stats.isDirectory()) {
            coreModuleFolders.concat(fs
                    .readdirSync('./update/install/modules')
                    .filter(function (file) {
                        return fs.statSync(path.join('./modules', file)).isDirectory();
                    }));
        }
    });

    // Get all installed module folder names
    var moduleFolders = fs
            .readdirSync('./modules')
            .filter(function (file) {
                return fs.statSync(path.join('./modules', file)).isDirectory();
            });

    // Zip only core and changed modules
    return moduleFolders.map(function (moduleFolder) {
        if (coreModuleFolders.indexOf(moduleFolder) > -1) {
            console.log('Create ' + moduleFolder + '.zip');
            return gulp
                    .src(path.join('./modules', moduleFolder) + '/**')
                    .pipe(zip(moduleFolder + '.zip'))
                    .pipe(gulp.dest('./update/modules'));
        }
        return false;
    });

});

// Create zip file of each core theme
gulp.task('update:_createThemeZipPackages', function () {

    // Define folder names of theme modules
    var coreThemeFolders = [
        'neoflow-backend'
    ];

    // Add folder names of changed modules
    fs.lstat('./update/install/themes', (err, stats) => {
        if (stats && stats.isDirectory()) {
            coreThemeFolders.concat(fs
                    .readdirSync('./update/install/themes')
                    .filter(function (file) {
                        return fs.statSync(path.join('./themes', file)).isDirectory();
                    }));
        }
    });

    // Get all installed theme folder names
    var themeFolders = fs
            .readdirSync('./themes')
            .filter(function (file) {
                return fs.statSync(path.join('./themes', file)).isDirectory();
            });

    // Zip only core and changed themes
    return themeFolders.map(function (themeFolder) {
        if (coreThemeFolders.indexOf(themeFolder) > -1) {
            console.log('Create ' + themeFolder + '.zip');
            return gulp
                    .src([
                        path.join('./themes', themeFolder) + '/**',
                        '!./themes/*/package*',
                        '!./themes/*/node_modules{,/**}',
                        '!./themes/*/src{,/**}',
                    ])
                    .pipe(zip(themeFolder + '.zip'))
                    .pipe(gulp.dest('./update/themes'));
        }
        return false;
    });
});