<?php $engine->startBlock('prepage'); ?>

<div class="row">
    <div class="col-md-6 mx-md-auto col-lg-4">

        <?= $view->renderAlertTemplate(); ?>

        <div class="card">

            <h4 class="card-header">
                2) <?= translate('Configure website'); ?>
            </h4>
            <div class="card-body">

                <form class="form-horizontal" method="post" action="<?= generate_url('install_website_create'); ?>">
                    <div class="form-group <?= has_validation_error('website_title', 'has-danger'); ?>">
                        <label for="inputWebsiteTitle">
                            <?= translate('Title'); ?> *
                        </label>
                        <input id="inputWebsiteTitle" type="text" required value="<?= $setting->website_title; ?>" class="form-control" name="website_title" maxlength="50" minlength="3" />
                    </div>

                    <hr />

                    <div class="form-group <?= has_validation_error('emailaddress', 'has-danger'); ?>">
                        <label for="inputSenderEmailaddress">
                            <?= translate('Email address'); ?> *
                        </label>
                        <input id="inputSenderEmailaddress" required type="email" value="<?= $setting->emailaddress; ?>" class="form-control" name="emailaddress" maxlength="100" />
                        <small class="form-text text-muted">
                            <?= translate('Default sender of emails from the website.'); ?>
                        </small>
                    </div>

                    <hr />

                    <div class="form-group">
                        <label for="selectDefaultLanguage">
                            <?= translate('Default language'); ?>
                        </label>
                        <select class="form-control" name="default_language_id" id="selectDefaultLanguage">
                            <?php foreach ($languages as $language) {
                                ?>
                                <option value="<?= $language->id(); ?>"  <?= ($language->id() == $activeLanguage->id() ? 'selected' : ''); ?>><?= translate($language->title); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="selectActiveLanguages">
                            <?= translate('Language', [], true); ?>
                        </label>
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

                    <div class="form-group">
                        <label for="selectTimezone">
                            <?= translate('Timezone'); ?>
                        </label>
                        <select class="form-control" name="timezone" id="selectTimezone" data-minimumResultsForSearch="1">
                            <?php foreach (get_timezones() as $region => $timezones) {
                                ?>
                                <optgroup label="<?= $region; ?>">
                                    <?php foreach ($timezones as $timezone => $title) {
                                        ?>
                                        <option value="<?= $timezone; ?>" <?= ($setting->timezone === $timezone ? 'selected' : ''); ?>><?= $title; ?></option>
                                    <?php }
                                    ?>
                                </optgroup>
                                <?php
                            }
                            ?>
                        </select>
                        <script>
                            document.getElementById('selectTimezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
                        </script>
                    </div>

                    <div class="form-group">
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

                </form>

            </div>
        </div>
    </div>
</div>
<?php
$engine->stopBlock();
