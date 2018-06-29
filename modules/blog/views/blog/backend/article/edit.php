<?= $view->renderTemplate('blog/backend/navbar') ?>

<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit article') ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('pmod_blog_backend_article_update') ?>">
                    <input value="<?= $article->id() ?>" type="hidden" name="article_id"/>
                    <input value="<?= $article->section_id ?>" type="hidden" name="section_id"/>

                    <div class="form-group row <?= has_validation_error('title', 'is-invalid') ?>">
                        <label for="inputTitle" class="col-sm-2 col-form-label">
                            <?= translate('Title') ?> *
                        </label>
                        <div class="col-sm-10">
                            <input type="text" value="<?= $article->title ?>" minlength="3" maxlength="100" name="title" id="inputTitle"
                                   required
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('abstract', 'is-invalid') ?>">
                        <label for="textareaAbstract" class="col-sm-2 col-form-label">
                            <?= translate('Abstract') ?>
                        </label>
                        <div class="col-sm-10">
                            <textarea maxlength="250" name="abstract" id="textareaAbstract" rows="4"
                                      class="form-control"><?= $article->abstract ?></textarea>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row <?= has_validation_error('category_ids', 'is-invalid') ?>">
                        <label for="selectCategories" class="col-sm-2 col-form-label">
                            <?= translate('Category', [], true) ?> *
                        </label>
                        <div class="col-sm-10">
                            <select multiple class="form-control" name="category_ids[]" id="selectCategories">
                                <?php
                                $category_ids = $article->getCategories()->mapProperty('category_id');
                                foreach ($categories as $category) {
                                    ?>
                                    <option value="<?= $category->id() ?>" <?= (in_array($category->id(), $category_ids) ? 'selected' : '') ?>>
                                        <?= $category->title ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('author_user_id', 'is-invalid') ?>">
                        <label for="selectCategories" class="col-sm-2 col-form-label">
                            <?= translate('Author') ?> *
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control" name="author_user_id" id="selectCategories">
                                <?php
                                foreach ($users as $user) {
                                    ?>
                                    <option value="<?= $user->id() ?>" <?= ($article->author_user_id == $user->id() ? 'selected' : '') ?>><?= $user->getFullname() ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row <?= has_validation_error('content', 'is-invalid') ?>">
                        <label for="wysiwygContent" class="col-sm-2 col-form-label">
                            <?= translate('Content') ?> *
                        </label>
                        <div class="col-sm-10">
                            <?= Neoflow\CMS\App::instance()
                                ->service('wysiwyg')
                                ->renderEditor($view, 'content', 'wysiwygContent', $article->content ?: '', '400px', [
                                    'uploadDirectory' => [
                                        'path' => $view->config()->getMediaPath('/modules/blog/section-' . $section->id()),
                                        'url' => $view->config()->getMediaUrl('/modules/blog/section-' . $section->id()),
                                    ]
                                ]);
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
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
                <?= translate('Edit metadata of website') ?>
            </h4>
            <div class="card-body">

                <form method="post" action="<?= generate_url('pmod_blog_backend_article_update_website') ?>">
                    <input value="<?= $article->id() ?>" type="hidden" name="article_id"/>
                    <input value="<?= $article->section_id ?>" type="hidden" name="section_id"/>

                    <div class="form-group row <?= has_validation_error('website_title', 'is-invalid') ?>">
                        <label for="inputWebsiteTitle" class="col-sm-3 col-form-label">
                            <?= translate('Website title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" value="<?= $article->website_title ?>" minlength="3" maxlength="100" name="website_title"
                                   id="inputWebsiteTitle" class="form-control"/>
                            <small class="form-text text-muted"><?= translate('Only required if the title of the website should contain a title other than that of the article (e.g. for SEO).') ?></small>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('website_description', 'is-invalid') ?>">
                        <label for="textareaWebsiteDescription" class="col-sm-3 col-form-label">
                            <?= translate('Website description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea maxlength="250" name="website_description" id="textareaWebsiteDescription" rows="4"
                                      class="form-control"><?= $article->website_description ?></textarea>
                            <small class="form-text text-muted"><?= translate('Only required if the description of the website should not contain the first 150 letters of the summary of the article (e.g. for SEO).') ?></small>
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

</div>
