<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-9 col-lg-7 col-xl-5 mx-md-auto">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">

            <h4 class="card-header">
                2) <?= translate('Configure website'); ?>
            </h4>
            <div class="card-body">

                <form class="form-horizontal" method="post" action="<?= generate_url('install_website_create'); ?>">
                    <div class="form-group row <?= has_validation_error('website_title', 'has-danger'); ?>">
                        <label for="inputWebsiteTitle" class="col-sm-4 col-form-label">
                            <?= translate('Title'); ?> *
                        </label>
                        <div class="col-sm-8">
                            <input id="inputWebsiteTitle" type="text" required value="<?= $setting->website_title; ?>" class="form-control" name="website_title" maxlength="50" minlength="3" />
                        </div>
                    </div>

                    <hr />

                    <div class="form-group row <?= has_validation_error('website_emailaddress', 'has-danger'); ?>">
                        <label for="inputSenderEmailaddress" class="col-sm-4 col-form-label">
                            <?= translate('Email address'); ?> *
                        </label>
                        <div class="col-sm-8">
                            <input id="inputSenderEmailaddress" required type="email" value="<?= $setting->website_emailaddress; ?>" class="form-control" name="website_emailaddress" maxlength="100" />
                            <small class="form-text text-muted">
                                <?= translate('Default sender of emails from the website.'); ?>
                            </small>
                        </div>
                    </div>

                    <hr />

                    <div class="form-group row">
                        <label for="selectDefaultLanguage" class="col-sm-4 col-form-label">
                            <?= translate('Default language'); ?>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="default_language_id" id="selectDefaultLanguage">
                                <?php foreach ($languages as $language) {
    ?>
                                    <option value="<?= $language->id(); ?>"  <?= ($language->id() == $activeLanguage->id() ? 'selected' : ''); ?>><?= translate($language->title); ?></option>
                                    <?php
}

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selectActiveLanguages" class="col-sm-4 col-form-label">
                            <?= translate('Language', [], true); ?>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="language_ids[]" multiple id="selectActiveLanguages">
                                <?php foreach ($languages as $language) {
                                    ?>
                                    <option value="<?= $language->id(); ?>"  <?= ($language->id() == $activeLanguage->id() ? 'selected' : ''); ?>><?= translate($language->title); ?></option>
                                <?php
                                }

                                ?>
                            </select>
                            <small class="form-text text-muted">
                                <?= translate('Supported languages of the website. Each language requires separate pages.'); ?>
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selectTimezone" class="col-sm-4 col-form-label">
                            <?= translate('Timezone'); ?>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="timezone" id="selectTimezone" data-minimumResultsForSearch="1">
                                <?php foreach (get_timezones() as $region => $timezones) {
                                    ?>
                                    <optgroup label="<?= $region; ?>">
                                        <?php foreach ($timezones as $timezone => $title) {
                                        ?>
                                            <option value="<?= $timezone; ?>" <?= ($setting->timezone === $timezone ? 'selected' : ''); ?>><?= $title; ?></option>
                                        <?php
                                    } ?>
                                    </optgroup>
                                <?php
                                }

                                ?>
                            </select>
                            <script>
                                document.getElementById('selectTimezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
                            </script>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-8 offset-sm-4">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Create'); ?>
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
</div>
<?php
$engine->stopBlock();
