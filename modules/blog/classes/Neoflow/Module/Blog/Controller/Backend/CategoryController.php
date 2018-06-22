<?php

namespace Neoflow\Module\Blog\Controller\Backend;

use Neoflow\CMS\Controller\Backend\AbstractPageModuleController;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Blog\Model\CategoryModel;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class CategoryController extends AbstractPageModuleController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $categories = CategoryModel::findAllByColumn('section_id', $this->section->id());

        return $this->render('blog/backend/category/index', [
            'categories' => $categories,
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

            // Create category
            $category = CategoryModel::create([
                'title' => $postData->get('title'),
                'abstract' => $postData->get('abstract'),
                'section_id' => $this->section->id(),
            ]);

            if ($category && $category->validate() && $category->save()) {
                $this->service('alert')->success(translate('Successfully created'));

                return $this->redirectToRoute('pmod_blog_backend_category_edit', [
                    'id' => $category->id(),
                    'section_id' => $this->section->id(),
                ]);
            } else {
                throw new RuntimeException('Create category failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('pmod_blog_backend_category_index', [
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
        $category = CategoryModel::findById($this->args['id']);
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $category = CategoryModel::updateById($data, $data['category_id']);
        }

        if ($category) {
            // Set title and breadcrumb
            $this->view
                ->setTitle($category->title)
                ->setSubtitle('ID: '.$category->id())
                ->addBreadcrumb(translate('Category', [], true), generate_url('pmod_blog_backend_category_index', [
                    'section_id' => $this->section->id(),
                ]));

            $url = generate_url('pmod_blog_frontend_article_index_category', [
                'blog' => $this->section->getPage()->getRelativeUrl(),
                'slug' => $category->title_slug,
            ]);

            $this->view->setPreviewUrl($category->getUrl().'#section-'.$this->section->id());

            // Set back url
            $this->view->setBackRoute('pmod_blog_backend_category_index', [
                'section_id' => $this->section->id(),
            ]);

            $articles = $category->articles()->orderByDesc('published_when')->fetchAll();

            return $this->render('blog/backend/category/edit', [
                'category' => $category,
                'articles' => $articles,
            ]);
        }

        throw new RuntimeException('Category not found (ID: '.$this->args['id'].')');
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
        // Get post data
        $postData = $this->request()->getPostData();

        $category = CategoryModel::updateById([
            'title' => $postData->get('title'),
            'description' => $postData->get('description'),
        ], $postData->get('category_id'));

        try {
            // Validate and save user
            if ($category && $category->validate() && $category->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating category failed (ID: '.$postData->get('category_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('pmod_blog_backend_category_edit', [
            'section_id' => $this->section->id(),
            'id' => $category->id(),
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
        try {
            if (CategoryModel::deleteById($this->args['id'])) {
                $this->service('alert')->success(translate('Successfully deleted'));
            } else {
                throw new RuntimeException('Deleting category failed (ID: '.$this->args['id'].')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning($ex->getErrors());
        }

        return $this->redirectToRoute('pmod_blog_backend_category_index', [
            'section_id' => $this->section->id(),
        ]);
    }
}
