<?php

use Neoflow\CMS\App;

$cacheName = App::instance()->get('cache')->getReflection()->getShortName();
if ('DummyCache' === $cacheName) {
    $cacheName = 'disabled';
}

$executionTime = round(\Neoflow\CMS\App::instance()->getExecutionTime(), 3);
?>

<div id="debugbar">
    <button data-target="#debugbarBody" data-toggle="collapse" class="btn btn-sm btn-primary btn-icon-right collapsed">
        Debug bar
        <span class="btn-icon"></span>
    </button>

    <ul class="list-inline d-inline">
        <li class="list-inline-item">
            Execution time: <small><?= $executionTime; ?></small>
        </li>
    </ul>

    <div id="debugbarBody" class="collapse collapse-history">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4">
                    <h4>General</h4>
                    <ul class="list-inline">
                        <li class="list-inline-item">Execution time: <small><?= $executionTime; ?></small></li>
                        <li class="list-inline-item">Cache: <small><?= $cacheName; ?></small></li>
                        <li class="list-inline-item">Log level: <small><?= ($view->logger()->getLoglevel() ?: 'none'); ?></small></li>
                    </ul>
                </div>
                <div class="col-sm-5">
                    <h4>Language</h4>
                    <ul class="list-inline">
                        <li class="list-inline-item">Language: <small><?= $view->translator()->getActiveLanguageCode(); ?></small></li>
                        <li class="list-inline-item">Date format: <small><?= $view->translator()->getDateFormat(); ?></small></li>
                        <li class="list-inline-item">Default language: <small><?= $view->translator()->getDefaultLanguageCode(); ?></small></li>
                        <li class="list-inline-item">Fallback language: <small><?= $view->translator()->getFallbackLanguageCode(); ?></small></li>
                    </ul>
                </div>
                <div class="col-sm-3">
                    <h4>Database</h4>
                    <ul class="list-inline">
                        <li class="list-inline-item">Executed queries: <small><?= App::instance()->get('executedQueries'); ?></small></li>
                        <li class="list-inline-item">Cached queries: <small><?= App::instance()->get('cachedQueries'); ?></small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>