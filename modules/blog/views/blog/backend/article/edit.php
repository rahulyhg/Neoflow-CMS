<?= $view->renderTemplate('blog/backend/article/navbar', [
    'article' => $article
]) ?>

<div class="row">
    <div class="col-xl-10">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Edit article') ?>
            </h4>

            <div class="card-body">

                <form class="form-change-check" method="post" action="<?= generate_url('pmod_blog_backend_article_update') ?>">
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


</div>
