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

        <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/flatly/bootstrap.min.css" rel="stylesheet" integrity="sha384-+ENW/yibaokMnme+vBLnHMphUYxHs34h9lpdbSLuAwGkOKFRl4C34WkjazBtb7eT" crossorigin="anonymous">

        <style type="text/css">
            .section-content {
                padding: 25px 0;
            }

            .block-content-red {
                background: red;
                color: #fff;
            }

            .block-content-red * {
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


        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only"><?= translate('Toggle navigation'); ?></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?= $view->getWebsiteTitle(); ?></a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <?= $view->renderNavigation('page-tree'); ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Link</a></li>
                    </ul>
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

        <div class="container">
asd
            <?= $view->renderBreadcrumbs(0, 5); ?>

        </div>

        <?= $view->renderBlock(); ?>

        <div class="container">

            <hr />

            <?= $view->renderNavigation('page-tree', 0, 5, 'frontend/debug-navigation'); ?>

            <hr />

            [[GoogleAnalytics?asd=123]]

            <ul class="list-inline">
                <li>
                    Execution time: <small><?= round(\Neoflow\CMS\App::instance()->getExecutionTime(), 3); ?></small>
                </li>
            </ul>
        </div>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <!-- Additional Javascript urls -->
        <?= $view->engine()->renderJavascriptUrls(); ?>

        <!-- Additional Javascript -->
        <?= $view->engine()->renderJavascript(); ?>
    </body>
</html>
