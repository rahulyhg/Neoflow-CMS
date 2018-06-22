<?php

namespace Neoflow\Module\Blog\Controller\Backend;

use Neoflow\CMS\Controller\Backend\AbstractPageModuleController;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Blog\Model\ArticleModel;
use Neoflow\Module\Blog\Model\CategoryModel;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class ArticleController extends AbstractPageModuleController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $articles = ArticleModel::findAllByColumn('section_id', $this->section->id());
        $categories = CategoryModel::findAllByColumn('section_id', $this->section->id());

        $users = UserModel::findAll();

        return $this->render('blog/backend/article/index', [
            'articles' => $articles,
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    /**
     * Create action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function createAction(): RedirectResponse
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Create user
            $article = ArticleModel::create([
                'title' => $postData->get('title'),
                'abstract' => $postData->get('abstract'),
                'category_ids' => $postData->get('category_ids'),
                'author_user_id' => $postData->get('author_user_id'),
                'section_id' => $this->section->id(),
            ]);

            if ($article && $article->validate() && $article->save()) {
                $this->service('alert')->success(translate('Successfully created'));

                return $this->redirectToRoute('pmod_blog_backend_article_edit', [
                    'id' => $article->id(),
                    'section_id' => $this->section->id(),
                ]);
            } else {
                throw new RuntimeException('Create article failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('pmod_blog_backend_article_index', [
            'section_id' => $this->section->id(),
        ]);
    }

    /**
     * Edit action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get user or data if validation has failed
        $article = ArticleModel::findById($this->args['id']);
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $article = ArticleModel::updateById($data, $data['user_id']);
        }

        if ($article) {
            // Set title and breadcrumb
            $this->view
                ->setTitle($article->title)
                ->setSubtitle('ID: '.$article->id())
                ->addBreadcrumb(translate('Blog', [], true), generate_url('pmod_blog_backend_article_index', [
                    $this->section->id(),
                ]));

            // Set back url
            $this->view->setBackRoute('pmod_blog_backend_article_index', [
                $this->section->id(),
            ]);

            return $this->render('blog/backend/article/edit', [
                'article' => $article,
            ]);
        }

        throw new RuntimeException('Article not found (ID: '.$this->args['id'].')');
    }

    /**
     * Update user action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateAction(): RedirectResponse
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Update user
            $user = UserModel::updateById([
                'email' => $postData->get('email'), 'firstname' => $postData->get('firstname'), 'lastname' => $postData->get('lastname'),
                'role_id' => $postData->get('role_id'),
            ], $postData->get('user_id'));

            // Validate and save user
            if ($user && $user->validate() && $user->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating user failed (ID: '.$postData->get('user_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_user_edit', ['id' => $postData->get('user_id')]);
    }

    /**
     * Delete action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        if (ArticleModel::deleteById($this->args['id'])) {
            $this->service('alert')->success(translate('Successfully deleted'));

            return $this->redirectToRoute('pmod_blog_backend_article_index', [
                'section_id' => $this->section->id(),
            ]);
        }
        throw new RuntimeException('Deleting article failed (ID: '.$this->args['id'].')');
    }
}
