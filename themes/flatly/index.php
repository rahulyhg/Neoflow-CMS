<!DOCTYPE html>
<html lang="de">
    <head>
        <title>
            <?= $view->getTitle(); ?> | <?= $view->getWebsiteTitle(); ?>
        </title>

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- Additional meta tags -->
        <?= $view->engine()->renderMetaTagProperties(); ?>

        <link href="<?= $view->getThemeUrl('css/bootstrap.min.css'); ?>" rel="stylesheet" />

        <style type="text/css">
            header {
                padding: 3rem 0 1rem;
            }

            .section-content {
                padding: 25px 0;
            }

            .block-content-primary {
                background: #2C3E50;
                color: #fff;
            }

            .block-content-primary * {
                color: #fff;
            }
        </style>


        <!-- Additional link tags -->
        <?= $view->engine()->renderLinkTagProperties(); ?>

        <!-- Additional CSS urls -->
        <?= $view->engine()->renderCssUrls(); ?>

        <!-- Additional CSS -->
        <?= $view->engine()->renderCss(); ?>

    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">

            <div class="container">

                <a class="navbar-brand" href="<?= $view->getWebsiteUrl(); ?>"><?= $view->getWebsiteTitle(); ?></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navigation">
                    <?= $view->renderNavigation('page-tree', 0, 2, 'frontend/navigation'); ?>
                </div>

            </div>

        </nav>

        <header>
            <div class="container">
                <div class="page-header">
                    <h1><?= $view->getTitle(); ?></h1>
                </div>
            </div>
        </header>

        <?= $view->renderBreadcrumbs(0, 2, 'frontend/breadcrumbs'); ?>

        <?= $view->renderSections(); ?>

        <footer>
            <div class="container">
                <hr />
                <ul class="list-inline">
                    <li class="list-inline-item">
                        Execution time: <?= round($view->getExecutionTime(), 3); ?>
                    </li>
                </ul>
            </div>
        </footer>

        [[GoogleAnalytics?id=123]]

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g=" crossorigin="anonymous"></script>
        <script src="<?= $view->getThemeUrl('js/bootstrap.min.js'); ?>"></script>

        <!-- Additional Javascript urls -->
        <?= $view->engine()->renderJavascriptUrls(); ?>

        <!-- Additional Javascript -->
        <?= $view->engine()->renderJavascript(); ?>
    </body>
</html>
