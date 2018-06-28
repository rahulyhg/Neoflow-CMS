<?= $view->renderTemplate('blog/backend/navbar') ?>

<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit category') ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('pmod_blog_backend_category_update') ?>">
                    <input value="<?= $category->id() ?>" type="hidden" name="category_id"/>
                    <input value="<?= $category->section_id ?>" type="hidden" name="section_id"/>
                    <div class="form-group row <?= has_validation_error('title', 'is-invalid') ?>">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title') ?> *
                        </label>
                        <div class="col-sm-9">
                            <input type="text" value="<?= $category->title ?>" minlength="3" maxlength="100" name="title" id="inputTitle" required
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('description', 'is-invalid') ?>">
                        <label for="textareaDescription" class="col-sm-3 col-form-label">
                            <?= translate('Description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea maxlength="250" name="description" id="textareaDescription" rows="4"
                                      class="form-control"><?= $category->description ?></textarea>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row <?= has_validation_error('website_title', 'is-invalid') ?>">
                        <label for="inputWebsiteTitle" class="col-sm-3 col-form-label">
                            <?= translate('Website title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" value="<?= $category->website_title ?>" minlength="3" maxlength="100" name="website_title"
                                   id="inputWebsiteTitle" class="form-control"/>
                            <small class="form-text text-muted"><?= translate('Only required if the title of the website should contain a title other than that of the category (e.g. because of SEO).') ?></small>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('website_description', 'is-invalid') ?>">
                        <label for="textareaWebsiteDescription" class="col-sm-3 col-form-label">
                            <?= translate('Website description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea maxlength="250" name="website_description" id="textareaWebsiteDescription" rows="4"
                                      class="form-control"><?= $category->website_description ?></textarea>
                            <small class="form-text text-muted"><?= translate('Only required if the description of the website should contain a different description than that of the category (e.g. because of SEO).') ?></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary btn-icon-left">
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

    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All articles') ?> (<?= $articles->count() ?>)
            </h4>
            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th data-priority="0" data-order="true">
                        <?= translate('Title') ?>
                    </th>
                    <th data-priority="1" data-init-order="desc" data-order="true">
                        <?= translate('Published on') ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($articles as $article) { ?>
                    <tr>
                        <td>
                            <a href="<?= generate_url('pmod_blog_backend_article_edit', [
                                'section_id' => $article->section_id,
                                'id' => $article->id(),
                            ]) ?>"
                               title="<?= translate('Edit article') ?>">
                                <?= $article->title ?>
                            </a>
                        </td>
                        <td>
                            <?= $article->getPublishedWhen() ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
