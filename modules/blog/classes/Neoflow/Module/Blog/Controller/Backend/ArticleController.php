<?php

namespace Neoflow\Module\Blog\Controller\Backend;

use Neoflow\CMS\Model\UserModel;
use Neoflow\CMS\View\Backend\SectionView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Blog\Controller\BackendController;
use Neoflow\Module\Blog\Model\ArticleModel;
use Neoflow\Module\Blog\Model\CategoryModel;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class ArticleController extends BackendController
{
    /**
     * @var ArticleModel|null
     */
    protected $article;

    /**
     * Constructor.
     *
     * @param SectionView $view Section view
     * @param array       $args Request arguments
     *
     * @throws RuntimeException
     */
    public function __construct(SectionView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->view->setTitle(translate('Article', [], true));

        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $this->article = ArticleModel::create($data);
        } elseif (isset($args['id'])) {
            $this->article = ArticleModel::findByColumns([
                'article_id' => $args['id'],
                'section_id' => $this->section->id(),
            ]);

            if (!$this->article) {
                throw new RuntimeException('Article not found (ID: '.$args['id'].' / SectionID: '.$this->section->id().')');
            }
        }

        if ($this->article) {
            $this->view
                ->setTitle($this->article->title)
                ->setSubtitle('ID: '.$this->article->id())
                ->addBreadcrumb(translate('Article', [], true), generate_url('pmod_blog_backend_article_index', [
                    'section_id' => $this->section->id(),
                ]));

            $this->view->setPreviewUrl($this->article->getUrl().'#section-'.$this->section->id());

            $this->view->setBackRoute('pmod_blog_backend_article_index', [
                'section_id' => $this->section->id(),
            ]);
        }
    }

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
            $article = ArticleModel::create([
                'title' => $this->request()->getPost('title'),
                'abstract' => $this->request()->getPost('abstract'),
                'category_ids' => $this->request()->getPost('category_ids'),
                'author_user_id' => $this->request()->getPost('author_user_id'),
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
     */
    public function editAction(): Response
    {
        $categories = CategoryModel::findAllByColumn('section_id', $this->section->id());

        return $this->render('blog/backend/article/edit', [
            'article' => $this->article,
            'categories' => $categories,
        ]);
    }

    /**
     * Update action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateAction(): RedirectResponse
    {
        $article = ArticleModel::updateById([
            'title' => $this->request()->getPost('title'),
            'abstract' => $this->request()->getPost('abstract'),
            'category_ids' => $this->request()->getPost('category_ids'),
            'author_user_id' => $this->request()->getPost('author_user_id'),
            'content' => $this->request()->getPost('content'),
        ], $this->request()->getPost('article_id'));

        try {
            if ($article && $article->validate() && $article->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating article failed (ID: '.$this->request()->getPost('article_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('pmod_blog_backend_article_edit', [
            'id' => $article->id(),
            'section_id' => $this->section->id(),
        ]);
    }

    /**
     * Edit metadata action.
     *
     * @return Response
     */
    public function editMetadataAction(): Response
    {
        $users = UserModel::findAll();

        return $this->render('blog/backend/article/edit_metadata', [
            'article' => $this->article,
            'users' => $users,
        ]);
    }

    /**
     * Update metadata action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateMetadataAction(): RedirectResponse
    {
        $data = [];
        if ('article' === $this->request()->getPost('type')) {
            $data = [
                'author_user_id' => $this->request()->getPost('author_user_id'),
            ];
        } elseif ('website' === $this->request()->getPost('type')) {
            $data = [
                'website_title' => $this->request()->getPost('website_title'),
                'website_description' => $this->request()->getPost('website_description'),
            ];
        }

        $article = ArticleModel::updateById($data, $this->request()->getPost('article_id'));

        try {
            if ($article && $article->validate() && $article->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating article failed (ID: '.$this->request()->getPost('article_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('pmod_blog_backend_article_edit_metadata', [
            'id' => $article->id(),
            'section_id' => $this->section->id(),
        ]);
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
