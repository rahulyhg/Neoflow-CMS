<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Default setting', [], true); ?>
            </h4>

            <div class="card-body">

                <form method="post" action="<?= generate_url('tmod_sitemap_backend_update_settings'); ?>" class="form-horizontal">
                    <div class="form-group row <?= has_validation_error('default_changefreq', 'has-error'); ?>">
                        <label for="selectDefaultChangefreq" class="col-sm-3 col-form-label">
                            <?= translate('Change frequency'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <select id="selectDefaultChangefreq" class="form-control" name="default_changefreq">
                                <?php foreach ($changeFrequencies as $changeFrequency) {

                                    ?>
                                    <option <?= ($changeFrequency === $settings->default_changefreq ? 'selected' : ''); ?> value="<?= $changeFrequency; ?>">
                                        <?= translate($changeFrequency); ?>
                                    </option>
                                    <?php
                                }

                                ?>
                            </select>
                            <small class="form-text text-muted">
                                <?= translate('Applies to URLs that are registered without change frequency.'); ?>
                            </small>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('default_priority', 'has-error'); ?>">
                        <label for="inputDefaultPriority" class="col-sm-3 col-form-label">
                            <?= translate('Priority'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <input value="<?= $settings->default_priority; ?>" id="inputDefaultPriority" type="text" class="form-control" required name="default_priority" maxlength="20" />
                            <small class="form-text text-muted">
                                <?= translate('Applies to URLs that are registered without priority.'); ?>
                            </small>
                        </div>
                    </div>

                    <hr />

                    <div class="form-group row <?= has_validation_error('default_changefreq', 'has-error'); ?>">
                        <label for="selectSitemapLifetime" class="col-sm-3 col-form-label">
                            <?= translate('Sitemap lifetime'); ?> *
                        </label>
                        <div class="col-sm-9">
                            <select id="selectSitemapLifetime" class="form-control" name="sitemap_lifetime">
                                <?php foreach ($sitemapLifetimes as $key => $value) {

                                    ?>
                                    <option <?= ($key === (int) $settings->sitemap_lifetime ? 'selected' : ''); ?> value="<?= $key; ?>">
                                        <?= translate($value); ?>
                                    </option>
                                    <?php
                                }

                                ?>
                            </select>
                            <small class="form-text text-muted">
                                <?= translate('Duration until the sitemap is automatically recreated.'); ?>
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9 <?= has_validation_error('automated_creation', 'has-danger'); ?>">
                            <input type="hidden" value="0" name="automated_creation" />
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" value="1" id="checkboxAutomatedCreation" name="automated_creation" <?= ($settings->automated_creation ? 'checked' : ''); ?>>
                                <label class="custom-control-label" for="checkboxAutomatedCreation"><?= translate('Auto generation'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save'); ?>
                            </button>

                            <span class="small float-right">
                                * = <?= translate('Required field', [], true); ?>
                            </span>
                        </div>
                    </div>
                </form>

            </div>

        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Manage sitemap'); ?>
            </h4>
            <div class="card-body">

                <ul class="list-unstyled">
                    <?php
                    if ($sitemapFile) {

                        ?>
                        <li><?= translate('Status'); ?>: <a href="<?= $sitemapFile->getUrl(); ?>" target="_blank" title="<?= translate('Show sitemap'); ?>"><?= translate('Available'); ?></a></li>
                        <li><?= translate('Last creation'); ?>: <?= format_timestamp($sitemapFile->clearFileCache()->getModificationTime()); ?>
                            <?php
                        } else {

                            ?>
                        <li><?= translate('Status'); ?>: <?= translate('Unavailable'); ?></li>
                        <li><?= translate('Last creation'); ?>: <?= translate('Unavailable'); ?></li>
                        <?php
                    }

                    ?>
                </ul>

                <a href="<?= generate_url('tmod_sitemap_backend_recreate'); ?>" class="btn btn-primary btn-icon-left" title="<?= translate('Recreate sitemap'); ?>">
                    <span class="btn-icon">
                        <i class="fa fa-sync"></i>
                    </span>
                    <?= translate('Recreate'); ?>
                </a>

                <?php if (!$settings->automated_creation) {

                    ?>
                    <a href="<?= generate_url('tmod_sitemap_backend_delete'); ?>" class="btn btn-outline-light btn-icon-left confirm-modal" data-message="<?= translate('Are you sure you want to delete it?'); ?>" title="<?= translate('Delete sitemap'); ?>">
                        <span class="btn-icon">
                            <i class="fa fa-trash-alt"></i>
                        </span>
                        <?= translate('Delete'); ?>
                    </a>
                    <?php
                }

                ?>
            </div>
        </div>

    </div>
</div>

