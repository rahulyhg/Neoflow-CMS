<?php

$id = ($id ? 'id="' . $id . '"' : '');
$height = ($height ? 'height="' . $height . '"' : '');

?>
<textarea name="<?= $name ?>" <?= $id ?> <?= $height ?> class="form-control" rows="10" width="100%"><?= $content ?></textarea>