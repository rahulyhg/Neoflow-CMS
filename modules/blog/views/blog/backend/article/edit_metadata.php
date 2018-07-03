<?= $view->renderTemplate('blog/backend/article/navbar', [
    'article' => $article
]) ?>

<div class="row">
    <div class="col-xl-6">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Article metadata') ?>
            </h4>

            <div class="card-body">

                <form class="form-change-check" method="post" action="<?= generate_url('pmod_blog_backend_article_update_metadata') ?>">
                    <input value="article" type="hidden" name="type"/>
                    <input value="<?= $article->id() ?>" type="hidden" name="article_id"/>
                    <input value="<?= $article->section_id ?>" type="hidden" name="section_id"/>

                    <div class="form-group row <?= has_validation_error('author_user_id', 'is-invalid') ?>">
                        <label for="selectCategories" class="col-sm-3 col-form-label">
                            <?= translate('Author') ?> *
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="author_user_id" id="selectCategories">
                                <?php foreach ($users as $user) { ?>
                                    <option value="<?= $user->id() ?>" <?= ($article->author_user_id == $user->id() ? 'selected' : '') ?>>
                                        <?= $user->getFullname() ?>
                                    </option>
                                <?php } ?>
                            </select>
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
    <div class="col-xl-6">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Website metadata') ?>
            </h4>

            <div class="card-body">

                <form class="form-change-check" method="post" action="<?= generate_url('pmod_blog_backend_article_update_metadata') ?>">
                    <input value="website" type="hidden" name="type"/>
                    <input value="<?= $article->id() ?>" type="hidden" name="article_id"/>
                    <input value="<?= $article->section_id ?>" type="hidden" name="section_id"/>

                    <div class="form-group row <?= has_validation_error('website_title', 'is-invalid') ?>">
                        <label for="inputWebsiteTitle" class="col-sm-3 col-form-label">
                            <?= translate('Website title') ?>
                        </label>
                        <div class="col-sm-9">
                            <input placeholder="<?= $article->title ?>" type="text" value="<?= $article->website_title ?>" minlength="3"
                                   maxlength="100" name="website_title"
                                   id="inputWebsiteTitle" class="form-control"/>
                            <small class="form-text text-muted"><?= translate('Only required if the title of the website should contain a title other than that of the article (e.g. for SEO).') ?></small>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('website_description', 'is-invalid') ?>">
                        <label for="textareaWebsiteDescription" class="col-sm-3 col-form-label">
                            <?= translate('Website description') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea maxlength="250" placeholder="<?= shortify($article->abstract, 250) ?>" name="website_description"
                                      id="textareaWebsiteDescription" rows="4"
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
