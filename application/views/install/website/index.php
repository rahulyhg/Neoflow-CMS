<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-5 mx-md-auto">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">

            <h4 class="card-header">
                2) <?= translate('Configure website'); ?>
            </h4>
            <div class="card-body">

                <form class="form-horizontal" method="post" action="<?= generate_url('install_website_create'); ?>">
                    <div class="form-group row <?= has_validation_error('website_title', 'has-danger'); ?>">
                        <label for="inputWebsiteTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputWebsiteTitle" type="text" required="" value="<?= $setting->website_title; ?>" class="form-control" name="website_title" maxlength="50" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('website_description', 'has-danger'); ?>">
                        <label for="textareaWebsiteDescription" class="col-sm-3 col-form-label">
                            <?= translate('Description'); ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="website_description" class="form-control vresize" maxlength="150" id="textareaWebsiteDescription" rows="3"><?= $setting->website_description; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('sender_emailaddress', 'has-danger'); ?>">
                        <label for="inputSenderEmailaddress" class="col-sm-3 col-form-label">
                            <?= translate('Email address'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputSenderEmailaddress" required="" type="email" value="<?= $setting->sender_emailaddress; ?>" class="form-control" name="sender_emailaddress" maxlength="100" />
                            <small class="form-text text-muted">
                                <?= translate('General e-mailaddress of the website (eg. info@yourdomain.tld)'); ?>
                            </small>
                        </div>
                    </div>

                    <hr />

                    <div class="form-group row">
                        <label for="selectDefaultLanguage" class="col-sm-3 col-form-label">
                            <?= translate('Default language'); ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="default_language_id" id="selectDefaultLanguage">
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
                        <label for="selectLanguages" class="col-sm-3 col-form-label">
                            <?= translate('Supported language', [], true); ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="language_ids[]" multiple id="selectLanguages">
                                <?php foreach ($languages as $language) {
                                    ?>
                                    <option value="<?= $language->id(); ?>"  <?= ($language->id() == $activeLanguage->id() ? 'selected' : ''); ?>><?= translate($language->title); ?></option>
                                <?php
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selectTimezone" class="col-sm-3 col-form-label">
                            <?= translate('Timezone'); ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="timezone" id="selectTimezone" data-minimumResultsForSearch="1">
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
                                } ?>
                            </select>
                            <script>
                                document.getElementById('selectTimezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
                            </script>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Create'); ?>
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<?php
$engine->stopBlock();
