<?= $view->renderTemplate('blog/backend/navbar') ?>


<div class="row">
    <div class="col-xl-7">

        <div class="card">
            <h4 class="card-header">
                <?= translate('All articles') ?>
            </h4>

            <table class="datatable table display responsive no-wrap" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th data-priority="0" data-order="true">
                        <?= translate('Title') ?>
                    </th>
                    <th data-priority="1" data-order="true" data-init-order="desc">
                        <?= translate('Published on') ?>
                    </th>
                    <th class="none">
                        <?= translate('Abstract') ?>
                    </th>
                    <th class="none">
                        <?= translate('Category', [], true) ?>
                    </th>
                    <th data-orderable="false" data-filterable="false" data-priority="0"></th>
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
                        <td>
                            <?= $article->abstract ?>
                        </td>
                        <td>
                            <?= $article->getCategories()->implode(function (\Neoflow\Module\Blog\Model\CategoryModel $category) use ($view) {
                                return '<a href="' . generate_url('pmod_blog_backend_category_edit', [
                                        'section_id' => $view->get('section')->id(),
                                        'id' => $category->id()
                                    ]) . '">' . $category->title . '</a>';
                            }) ?>
                        </td>
                        <td class="text-right nowrap">
                            <a href="<?= generate_url('pmod_blog_backend_article_edit', [
                                'section_id' => $article->section_id,
                                'id' => $article->id(),
                            ]) ?>"
                               class="btn btn-outline-light btn-sm btn-icon-left d-none d-xl-inline-block"
                               title="<?= translate('Edit article') ?>">
                                    <span class="btn-icon">
                                        <i class="fa fa-pencil-alt"></i>
                                    </span>
                                <?= translate('Edit') ?>
                            </a>
                            <a href="<?= generate_url('pmod_blog_backend_article_delete', [
                                'section_id' => $article->section_id,
                                'id' => $article->id(),
                            ]) ?>"
                               class="btn btn-primary btn-sm confirm-modal"
                               data-message="<?= translate('Are you sure you want to delete it?') ?>"
                               title="<?= translate('Delete article') ?>">
                                <i class="fa fa-fw fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>

    </div>
    <div class="col-xl-5">

        <div class="card">
            <h4 class="card-header">
                <?= translate('Create article') ?>
            </h4>
            <div class="card-body">
                <form method="post" action="<?= generate_url('pmod_blog_backend_article_create') ?>">
                    <input type="hidden" name="section_id" value="<?= $view->get('section')->id() ?>"/>
                    <div class="form-group row <?= has_validation_error('title', 'is-invalid') ?>">
                        <label for="inputTitle" class="col-sm-3 col-form-label">
                            <?= translate('Title') ?> *
                        </label>
                        <div class="col-sm-9">
                            <input type="text" minlength="3" maxlength="100" name="title" id="inputTitle" required class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group row <?= has_validation_error('abstract', 'is-invalid') ?>">
                        <label for="textareaAbstract" class="col-sm-3 col-form-label">
                            <?= translate('Abstract') ?>
                        </label>
                        <div class="col-sm-9">
                            <textarea maxlength="500" name="abstract" id="textareaAbstract" rows="4" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('category_ids', 'is-invalid') ?>">
                        <label for="selectCategories" class="col-sm-3 col-form-label">
                            <?= translate('Category', [], true) ?> *
                        </label>
                        <div class="col-sm-9">
                            <select multiple class="form-control" name="category_ids[]" id="selectCategories">
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?= $category->id() ?>"><?= $category->title ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row <?= has_validation_error('author_user_id', 'is-invalid') ?>">
                        <label for="selectCategories" class="col-sm-3 col-form-label">
                            <?= translate('Author') ?> *
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" name="author_user_id" id="selectCategories">
                                <?php
                                $authUser = $view->service('auth')->getUser();
                                foreach ($users as $user) {
                                    ?>
                                    <option value="<?= $user->id() ?>" <?= ($authUser->id() === $user->id() ? 'selected' : '') ?>><?= $user->getFullname() ?></option>
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
</div>

