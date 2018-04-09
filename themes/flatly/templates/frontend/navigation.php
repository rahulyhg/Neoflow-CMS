<ul class="navbar-nav mr-auto">
    <?php
    foreach ($navigation as $navTreeItem) {
        if (count($navTreeItem['children'])) {

            ?>
            <li class="nav-item <?= ($navTreeItem['status'] ? 'active' : ''); ?> dropdown">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-expanded="false">
                    <?= $navTreeItem['title']; ?> <span class="sr-only">(current)</span>
                </a>
                <div class="dropdown-menu">
                    <?php foreach ($navTreeItem['children'] as $navTreeChild) { ?>
                        <a class="dropdown-item" href="<?= $navTreeChild['relative_url']; ?>">
                            <?= $navTreeChild['title']; ?>
                        </a>
                    <?php } ?>
                </div>
            </li>
            <?php
        } else {

            ?>
            <li class="nav-item <?= ($navTreeItem['status'] ? 'active' : ''); ?>">
                <a class="nav-link" href="<?= $navTreeItem['relative_url']; ?>">
                    <?= $navTreeItem['title']; ?>
                </a>
            </li>
            <?php
        }
    }

    ?>
</ul>