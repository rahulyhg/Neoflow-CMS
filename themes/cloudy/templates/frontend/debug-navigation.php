<ul>
    <?php foreach ($navigation as $item) {
    ?>
        <li>
            <a<?= ($item['status'] ? ' class="'.$item['status'].'"' : ''); ?> href="<?= $item['relative_url']; ?>"><?= $item['title']; ?></a>
            <ul class="list-inline">
                <li><small class="text-muted">Level: <?= $item['level']; ?></small></li>
                <li><small class="text-muted">ID: <?= $item['page']->id(); ?></small></li>
                <li><small class="text-muted">Status: <?= $item['status'] ?: '-'; ?></small></li>

            </ul>



            <?php
            if (count($item['children'])) {
                echo $view->renderTemplate(__FILE__, [
                    'navigation' => $item['children'],
                ]);
            } ?>
        </li>
    <?php
} ?>
</ul>