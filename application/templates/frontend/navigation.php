<ul>
    <?php foreach ($navigation as $item) {
    ?>
        <li>
            <a class="<?= $item['status'] ?>" href="<?= $item['relative_url'] ?>"><?= $item['title'] ?></a>
            <?php
            if (count($item['children'])) {
                echo $view->renderTemplate(__FILE__, [
                    'navigation' => $item['children'],
                ]);
            } ?>
        </li>
    <?php
}

?>
</ul>