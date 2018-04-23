# Neoflow CMS
Module-based content management system with a user friendly interface, based on a perfect optimized MVC framework.

## Current state
The Neoflow CMS is currently in development. But the first prerelease is planned for the comming few months (~march 2018)

## Requirements
 * About 20 MB disk space
 * Apache 2.4 or newer
 * PHP 7.1 or a newer
 * MySQL 5 (or MariaDB 10) or newer

## Manual

### Installation

1. Create a database schema (we recommend utf8mb4_unicode_ci as collation)
2. Create a user for the database schema and grant it with data definition (CREATE, DROP, ALTER, ...) and data manipulation (SELECT, INSERT, DELETE, UPDATE, ...) permissions
3. [Download](https://github.com/Neoflow/Neoflow-CMS/releases/latest) and unpack the installation package of the latest release.
4. Upload or move the file and folders to the root directory of your website. The index.php needs to be in the root directory of your website (e.g. /www_root/mysite.tld/index.php)
5. Open the website in your browser and follow the instructions of the installation wizard.
5. **Done, enjoy your new website!** :smiley:

### Update

1. Download the update package.
2. Upload and install the update package in the backend under maintenance

If you have trouble to upload the update package then change the upload settings of your PHP installation to 10MB.
```
upload_max_filesize = 10M
post_max_size = 10M
```

## License

The Neoflow CMS is licensed under MIT.

*Made in Switzerland with :sparkling_heart:
