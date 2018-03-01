<?php if ($exception) {
    ?>
    <h2>
        500 - <?= get_class($exception); ?>: <?= $exception->getMessage(); ?> on line <?= $exception->getLine(); ?>
    </h2>
    <hr />
    <p class="small">
        <?= get_exception_trace($exception, true, true); ?>
    </p>
    <hr />
    <p>
        <?= date('c'); ?>
    </p>
<?php
} else {
        ?>
    <h2>500</h2>
<?php
    } ?>
