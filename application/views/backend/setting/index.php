<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <h4 class="card-header">
                <?= translate('General setting', [], true) ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_setting_update') ?>">
                    <input type="hidden" value="general" name="type"/>
                    <div class="form-group row <?= has_validation_error('website_title', 'has-danger') ?>">
                        <label for="inputWebsiteTitle" class="col-sm-3 col-form-label">
                            <?= translate('Website title') ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputWebsiteTitle" type="text" required value="<?= $setting->website_title ?>" class="form-control"
                                   name="website_title" maxlength="50" minlength="3"/>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('website_description', 'has-danger') ?>">
                        <label for="textareaWebsiteDescription" class="col-sm-3 col-form-label">
                            <?= translate('Website description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea name="website_description" class="form-control vresize" maxlength="150" id="textareaWebsiteDescription"
                                      rows="3"><?= $setting->website_description ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('website_keywords', 'has-danger') ?>">
                        <label for="selectWebsiteKeywords" class="col-sm-3 col-form-label">
                            <?= translate('Website keyword', [], true) ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" data-token-separators="[',']" data-tags="true" name="website_keywords[]" multiple
                                    id="selectWebsiteKeywords">
                                <?php foreach ($setting->getWebsiteKeywords() as $websiteKeyword) { ?>
                                    <option value="<?= $websiteKeyword ?>" selected><?= $websiteKeyword ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('website_author', 'has-danger') ?>">
                        <label for="inputWebsiteAuthor" class="col-sm-3 col-form-label">
                            <?= translate('Website author') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputWebsiteAuthor" type="text" value="<?= $setting->website_author ?>" class="form-control"
                                   name="website_author" maxlength="50"/>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row <?= has_validation_error('emailaddress', 'has-danger') ?>">
                        <label for="inputSenderEmailaddress" class="col-sm-3 col-form-label">
                            <?= translate('Email address') ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputSenderEmailaddress" required="" type="email" value="<?= $setting->emailaddress ?>" class="form-control"
                                   name="emailaddress" maxlength="100"/>
                            <small class="form-text text-muted">
                                <?= translate('Default sender of emails from the website.') ?>
                            </small>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row">
                        <label for="selectDefaultLanguage" class="col-sm-3 col-form-label">
                            <?= translate('Default language') ?> *
                        </label>
                        <div class="col-sm-9">
                            <select id="selectDefaultLanguage" class="form-control" name="default_language_id">
                                <?php foreach ($languages as $language) { ?>
                                    <option value="<?= $language->id() ?>" <?= ($language->id() == $setting->default_language_id ? 'selected' : '') ?>><?= translate($language->title) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selectLanguages" class="col-sm-3 col-form-label">
                            <?= translate('Language', [], true) ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="language_ids[]" multiple id="selectLanguages">
                                <?php foreach ($languages as $language) { ?>
                                    <option value="<?= $language->id() ?>" <?= (in_array($language->id(), $setting->language_ids) ? 'selected' : '') ?>>
                                        <?= translate($language->title) ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <small class="form-text text-muted">
                                <?= translate('Supported languages of the website. Each language requires separate pages.') ?>
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selectTimezone" class="col-sm-3 col-form-label">
                            <?= translate('Timezone') ?> *
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="timezone" id="selectTimezone" data-minimumResultsForSearch="1">
                                <?php foreach (get_timezones() as $region => $timezones) { ?>
                                    <optgroup label="<?= $region ?>">
                                        <?php foreach ($timezones as $timezone => $title) { ?>
                                            <option value="<?= $timezone ?>" <?= ($setting->timezone === $timezone ? 'selected' : '') ?>>
                                                <?= $title ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <hr/>

                    <div class="form-group row <?= has_validation_error('allowed_file_extensions', 'has-danger') ?>">
                        <label for="inputAllowedFileExtensions" class="col-sm-3 col-form-label">
                            <?= translate('Allowed file extension', [], true) ?>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" data-tags="true" name="allowed_file_extensions[]" multiple id="inputAllowedFileExtensions">
                                <?php foreach ($setting->getAllowedFileExtensions() as $allowedFileExtension) { ?>
                                    <option value="<?= $allowedFileExtension ?>" selected>
                                        <?= $allowedFileExtension ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" name="saveSubmit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save') ?>
                            </button>

                            <span class="small float-right">
                                * = <?= translate('Required field', [], true) ?>
                            </span>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card">
            <h4 class="card-header">
                <?= translate('Security setting', [], true) ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_setting_update') ?>">
                    <input type="hidden" value="security" name="type"/>
                    <div class="form-group row <?= has_validation_error('login_attempts', 'has-danger') ?>">
                        <label for="inputLoginAttempts" class="col-sm-3 col-form-label">
                            <?= translate('Login attempt', [], true) ?> *
                        </label>
                        <div class="col-sm-9">
                            <input id="inputLoginAttempts" required type="number" min="3" value="<?= $setting->login_attempts ?>" class="form-control"
                                   name="login_attempts"/>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('session_lifetime', 'has-danger') ?>">
                        <label for="inputSessionLifetime" class="col-sm-3 col-form-label">
                            <?= translate('Session lifetime') ?> *
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input id="inputSessionLifetime" required type="number" min="300" value="<?= $setting->session_lifetime ?>"
                                       class="form-control" name="session_lifetime"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <?= translate('Second', [], true) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('session_name', 'has-danger') ?>">
                        <label for="inputSessionName" class="col-sm-3 col-form-label">
                            <?= translate('Session name') ?>
                        </label>
                        <div class="col-sm-9">
                            <input id="inputSessionName" type="text" value="<?= $setting->session_name ?>" class="form-control" name="session_name"/>
                            <small class="form-text text-muted">
                                <?= translate('If you change the session name, you will be logged out automatically after saving.') ?>
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" name="saveSubmit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save') ?>
                            </button>

                            <span class="small float-right">
                                * = <?= translate('Required field', [], true) ?>
                            </span>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
    <div class="col-xl-6">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Theme setting', [], true) ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('backend_setting_update') ?>">
                    <input type="hidden" value="theme" name="type"/>
                    <div class="form-group row">
                        <label for="selectTheme" class="col-sm-3 col-form-label">
                            <?= translate('Theme') ?> *
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="theme_id" id="selectTheme">
                                <?php
                                foreach ($themes as $theme) {
                                    if ('frontend' === $theme->type) {
                                        ?>
                                        <option value="<?= $theme->id() ?>" <?= ($setting->theme_id = $theme->id() ? 'selected' : '') ?>><?= $theme->name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('custom_css', 'has-danger') ?>">
                        <label for="textareaCustomCss" class="col-sm-3 col-form-label">
                            <?= translate('Custom CSS') ?>
                        </label>
                        <div class="col-sm-9">
                            <?php
                            if (Neoflow\CMS\App::instance()->hasService('code')) {
                                echo Neoflow\CMS\App::instance()
                                    ->service('code')
                                    ->renderEditor('custom_css', 'textareaCustomCss', $setting->custom_css, '150px', ['mode' => 'text/css']);
                            } else {
                                ?>
                                <textarea name="custom_css" class="form-control vresize" id="textareaFrontendCss"
                                          rows="5"><?= $setting->custom_css ?></textarea>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9 <?= has_validation_error('show_custom_css', 'has-danger') ?>">
                            <input type="hidden" value="0" name="show_custom_css"/>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="checkboxShowCustomCss" class="custom-control-input" value="1"
                                       name="show_custom_css" <?= ($setting->show_custom_css ? 'checked' : '') ?>>
                                <label class="custom-control-label" for="checkboxShowCustomCss"><?= translate('Show custom CSS') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('custom_js', 'has-danger') ?>">
                        <label for="textareaCustomJs" class="col-sm-3 col-form-label">
                            <?= translate('Custom JavaScript') ?>
                        </label>
                        <div class="col-sm-9">
                            <?php
                            if (Neoflow\CMS\App::instance()->hasService('code')) {
                                echo Neoflow\CMS\App::instance()
                                    ->service('code')
                                    ->renderEditor('custom_js', 'textareaCustomJs', $setting->custom_js, '150px', ['mode' => 'text/javascript']);
                            } else {
                                ?>
                                <textarea name="custom_js" class="form-control vresize" id="textareaCustomJs"
                                          rows="5"><?= $setting->custom_js ?></textarea>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9 <?= has_validation_error('show_custom_js', 'has-danger') ?>">
                            <input type="hidden" value="0" name="show_custom_js"/>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="checkboxShowCustomJs" class="custom-control-input" value="1"
                                       name="show_custom_js" <?= ($setting->show_custom_js ? 'checked' : '') ?>>
                                <label class="custom-control-label" for="checkboxShowCustomJs"><?= translate('Show custom JavaScript') ?></label>
                            </div>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row">
                        <label for="selectBackendTheme" class="col-sm-3 col-form-label">
                            <?= translate('Backend theme') ?> *
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="backend_theme_id" id="selectBackendTheme">
                                <?php
                                foreach ($themes as $theme) {
                                    if ('backend' === $theme->type) {
                                        ?>
                                        <option value="<?= $theme->id() ?>" <?= ($setting->theme_id = $theme->id() ? 'selected' : '') ?>><?= $theme->name ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-4 <?= has_validation_error('show_debugbar', 'has-danger') ?>">
                            <input type="hidden" value="0" name="show_debugbar"/>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="checkboxShowDebugbar" class="custom-control-input" value="1"
                                       name="show_debugbar" <?= ($setting->show_debugbar ? 'checked' : '') ?>>
                                <label class="custom-control-label" for="checkboxShowDebugbar"><?= translate('Show debugbar') ?></label>
                            </div>
                        </div>
                        <div class="col-sm-5 <?= has_validation_error('show_error_details', 'has-danger') ?>">
                            <input type="hidden" value="0" name="show_error_details"/>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" value="1" id="checkboxShowErrorDetails"
                                       name="show_error_details" <?= ($setting->show_error_details ? 'checked' : '') ?>>
                                <label class="custom-control-label" for="checkboxShowErrorDetails"><?= translate('Show error details') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" name="saveSubmit" class="btn btn-primary btn-icon-left">
                                <span class="btn-icon">
                                    <i class="fa fa-save"></i>
                                </span>
                                <?= translate('Save') ?>
                            </button>

                            <span class="small float-right">
                                * = <?= translate('Required field', [], true) ?>
                            </span>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="card">
            <h4 class="card-header">
                <?= translate('Advanced settings') ?>
            </h4>
            <div class="card-body">
                <p>
                    <?= translate('Advanced settings description') ?>
                </p>
                <h4>
                    <?= translate('Path to the config file') ?>:
                </h4>
                <ul>
                    <li>
                        <i><?= $view->config()->getPath('/config.php') ?></i>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

