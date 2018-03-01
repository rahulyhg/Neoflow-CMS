var gulp = require('gulp'),
        shell = require('shelljs'),
        zip = require('gulp-zip'),
        run = require('gulp-run');

var pjson = require('./package.json');
// git archive -o update.zip HEAD $(git diff --name-only 9b339fd)
// git checkout-index -f --prefix="C:/aaa/" $(git diff --name-only 9b339fd)

var config = {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'neoflow-cms',
    commands: {
        mysql: 'mysql',
        mysqldump: 'mysqldump',
    },
    dumpFilePath: './/installation//neoflow-cms.sql'
};

gulp.task('mysql:exportSqlDump', function () {
    var command = config.commands.mysqldump + ' -u ' + config.user + ' ' + config.database + ' > ' + config.dumpFilePath;
    console.log(command);
    var result = shell.exec(command);
    if (result.code !== 0) {
        console.error('MySQL dump export failed');
    } else {
        console.log('MySQL dump successful exported');
    }
    shell.exit(1);
});

gulp.task('mysql:importSqlDump', function () {
    var command = config.commands.mysql + ' -u ' + config.user + ' ' + config.database + ' < ' + config.dumpFilePath;
    console.log(command);
    var result = shell.exec(command);
    if (result.code !== 0) {
        console.error('MySQL dump import failed');
    } else {
        console.log('MySQL dump successful imported');
    }
    shell.exit(1);
});

gulp.task('mysql:cleanDatabase', function () {
    var command = config.commands.mysql + ' -u ' + config.user + ' -Bse "' +
            'DROP DATABASE `' + config.database + '`;' +
            'CREATE DATABASE `' + config.database + '` CHARACTER SET utf8 COLLATE utf8_bin;"';
    console.log(command);
    var result = shell.exec(command);
    if (result.code !== 0) {
        console.error('MySQL clean database failed');
    } else {
        console.log('MySQL database successful cleaned');
    }
    shell.exit(1);
});


gulp.task('release:build', function () {
    var dir = '.';
    return gulp.src([
        dir + '/**',
        '!' + dir + '/config.php',
        '!' + dir + '/package*',
        '!' + dir + '/gulpfile.js',
        '!' + dir + '/node_modules{,/**}',
        '!' + dir + '/nbproject{,/**}',
        '!' + dir + '/src{,/**}',
        '!' + dir + '/robots.txt',
        '!' + dir + '/sitemap.xml',
        '!' + dir + '/temp/update{,/**}',
        '!' + dir + '/themes/*/package*',
        '!' + dir + '/themes/*/node_modules{,/**}',
        '!' + dir + '/themes/*/src{,/**}',
        '!' + dir + '/*.zip'
    ])
            .pipe(zip(pjson.name + '-' + pjson.version + '.zip'))
            .pipe(gulp.dest(dir + '/'));
});



gulp.task('update:fetch', function () {
    var tag = '9b339fd'; // Commit or tag
    var dir = './temp/update';
    return gulp
            .src(dir + '/install', {read: false})
            .pipe(run('git checkout-index -f --prefix="<%= file.path %>/" $(git diff --name-only ' + tag + ')', {
                usePowerShell: true,
                verbosity: 0
            }))
            .on('end', function () {
                return gulp.src([
                    dir + '/**',
                    '!' + dir + '/install/config.php',
                    '!' + dir + '/install/package*',
                    '!' + dir + '/install/gulpfile.js',
                    '!' + dir + '/install/node_modules{,/**}',
                    '!' + dir + '/install/install{,/**}',
                    '!' + dir + '/install/nbproject{,/**}',
                    '!' + dir + '/install/src{,/**}',
                    '!' + dir + '/install/robots.txt',
                    '!' + dir + '/install/sitemap.xml',
                    '!' + dir + '/install/temp/update{,/**}',
                    '!' + dir + '/install/themes/*/package*',
                    '!' + dir + '/install/themes/*/node_modules{,/**}',
                    '!' + dir + '/install/themes/*/src{,/**}',
                    '!' + dir + '/*.zip'
                ])
                        .pipe(zip(pjson.name + '-' + pjson.version + '-update.zip'))
                        .pipe(gulp.dest(dir + '/'));
            });

});