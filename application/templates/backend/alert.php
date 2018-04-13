<?php foreach ($alerts as $alert) {
    ?>

    <div class="alert alert-<?= $alert->getType(); ?>">
        <?php
        foreach ($alert->getMessages() as $message) {
            if (is_array($message) && count($message) > 0) {
                ?>
                <ul><li><?= implode('</li><li>', $message); ?></li></ul>
            <?php
            } else {
                ?>
                <p><?= $message; ?></p>
                <?php
            }
        } ?>
    </div>
<?php
} ?>