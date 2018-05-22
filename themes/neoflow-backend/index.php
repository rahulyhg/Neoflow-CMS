<!DOCTYPE html>
<html lang="<?= $view->translator()->getCurrentLanguageCode(); ?>">
    <head>
        <title>
            <?= $view->getTitle(); ?> | <?= $view->getWebsiteTitle(); ?>
        </title>

        <!-- Meta data -->
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="<?= $view->getThemeUrl('/img/favicons/apple-touch-icon.png?{version}'); ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= $view->getThemeUrl('/img/favicons/favicon-32x32.png?{version}'); ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= $view->getThemeUrl('/img/favicons/favicon-16x16.png?{version}'); ?>">
        <link rel="icon" type="image/ico" href="<?= $view->getThemeUrl('/img/favicons/favicon.ico?{version}'); ?>" />
        <link rel="manifest" href="<?= $view->getThemeUrl('/img/favicons/manifest.json?{version}'); ?>">
        <link rel="mask-icon" href="<?= $view->getThemeUrl('/img/favicons/safari-pinned-tab.svg?{version}'); ?>" color="#d55d4e">
        <meta name="theme-color" content="#d55d4e">

        <!-- Additional meta tags -->
        <?= $view->engine()->renderMetaTagProperties(); ?>

        <!-- Additional CSS urls -->
        <?= $engine->renderCssUrls(); ?>

        <!-- Additional CSS -->
        <?= $engine->renderCss(); ?>

        <!-- Theme stylesheets -->
        <link href="<?= $view->getThemeUrl('/css/style.css?{version}'); ?>" rel="stylesheet" />

        <!-- Additional head Javascript -->
        <?= $engine->renderJavascript('head'); ?>

        <!-- Additional head Javascript urls -->
        <?= $engine->renderJavascriptUrls('head'); ?>

    </head>

    <body class="fixed-nav <?= ($engine->hasBlock('prepage') ? 'prepage' : '') . ($engine->hasBlock('plain') ? 'plain' : ''); ?>">

        <?php if ($engine->hasBlock('plain')) { ?>
            <div class="container-fluid">
                <?= $engine->renderBlock('plain'); ?>
            </div>
        <?php } else { ?>

            <?= $view->renderTemplate('navigation/top'); ?>
            <?php if (!$engine->hasBlock('prepage')) { ?>
                <script>
                    if (localStorage.getItem('side-navigation') === 'minimized') {
                        document.body.classList.add('side-navigation-minimized');
                    }
                </script>
                <?= $view->renderTemplate('navigation/side'); ?>
            <?php } ?>

            <div id="content">
                <?php if ($engine->hasBlock('prepage')) { ?>
                    <div class="container-fluid">
                        <div id="prepage">
                            <?= $view->renderTemplate('prepage/header'); ?>
                            <?= $engine->renderBlock('prepage'); ?>
                            <?= $view->renderTemplate('prepage/footer'); ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <?= $view->renderTemplate('header'); ?>
                    <div class="container-fluid">
                        <div class="content-body">
                            <?= $view->renderAlertTemplate(); ?>
                            <?= $engine->renderBlock('view'); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <?php if ($view->settings()->show_debugbar) { ?>
                <?= $view->renderTemplate('debugbar'); ?>
            <?php } ?>

        <?php } ?>

        <?= $view->renderTemplate('modal/relogin'); ?>
        <?= $view->renderTemplate('modal/confirm'); ?>
        <?= $view->renderTemplate('modal/alert'); ?>

        <!-- Theme vars -->
        <script>
            var URL = '<?= $view->config()->getUrl(); ?>',
                    THEME_URL = '<?= $view->getThemeUrl(); ?>',
                    LANGUAGE_CODE = '<?= $view->translator()->getCurrentLanguageCode(); ?>';
        </script>

        <!-- Theme Javascript -->
        <script src="<?= $view->getThemeUrl('/js/script.js?{version}'); ?>"></script>

        <!-- Additional Javascript urls -->
        <?= $engine->renderJavascriptUrls(); ?>

        <!-- Additional Javascript -->
        <?= $engine->renderJavascript(); ?>
    </body>
</html>
