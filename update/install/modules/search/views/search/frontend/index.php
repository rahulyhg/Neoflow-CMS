<form class="mb-3">
    <div class="form-row">
        <div class="col-sm-8 col-md-10">
            <input type="text" name="q" value="<?= $query ?>" class="form-control mb-2 mb-sm-0">
        </div>
        <div clasS="col-sm-4 col-md-2">
            <button type="submit" class="btn btn-primary btn-block">
                <?= translate('Search'); ?>
            </button>
        </div>
    </div>
</form>


<?php if ($results->count() > 0) { ?>
    <ul class="list-group list-group-flush">
        <?php foreach ($results as $result) { ?>
            <li class="list-group-item">
                <p>
                    <a href="<?= $result->getUrl() ?>"><?= $result->getTitle() ?></a>
                </p>
                <p>
                    <?= preg_replace("/(" . $query . ")/i", "<strong>$1</strong>", $result->getFocusedDescription($query)); ?>
                </p>
            </li>
        <?php } ?>
    </ul>
<?php } else if ($query) { ?>
    <?= translate('No results found') ?>
<?php } ?>


