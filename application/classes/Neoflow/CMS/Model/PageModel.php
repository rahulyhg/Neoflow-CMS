<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\CMS\View\FrontendView;
use Neoflow\Framework\HTTP\Exception\ForbiddenException;
use Neoflow\Framework\HTTP\Exception\NotFoundException;
use Neoflow\Framework\HTTP\Exception\UnauthorizedException;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class PageModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'pages';

    /**
     * @var string
     */
    public static $primaryKey = 'page_id';

    /**
     * @var array
     */
    public static $properties = ['page_id', 'title', 'slug',
        'description', 'keywords', 'is_active', 'is_restricted', 'author_user_id',
        'only_logged_in_users', 'language_id', 'url', 'has_custom_slug', 'is_startpage',
        'created_when', 'modified_when', ];

    /**
     * Get repository to fetch sections.
     *
     * @return Repository
     */
    public function sections()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\SectionModel', 'page_id');
    }

    /**
     * Get status whether page is accessible (for current user).
     *
     * @return bool
     *
     * @throws UnauthorizedException
     * @throws ForbiddenException
     */
    public function isAccessible()
    {
        if ($this->is_active) {
            // Anonymous access
            if (!$this->only_logged_in_users) {
                return true;
            }

            $user = $this->getService('auth')->getUser();
            $roles = $this->roles()->fetchAll();

            if ($user) {
                // Get role ids
                $roleIds = $roles->mapValue('role_id');

                // Role-based access
                if (count($roleIds)) {
                    // Admin role id (admin has access everywhere)
                    $roleIds[] = 1;

                    // Check if user has accessible role
                    return in_array($user->role_id, $roleIds);
                }

                // Only authenticated access
                return true;
            }
        }

        return false;
    }

    /**
     * Get page url.
     *
     * @param bool $forStartpage SET true to get the URL path for the startpage too
     *
     * @return string
     */
    public function getUrl(bool $forStartpage = false): string
    {
        return $this->config()->getUrl($this->getRelativeUrl(false, true, $forStartpage));
    }

    /**
     * Validate url.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function validateUrl(): bool
    {
        $numberOfPages = self::repo()
            ->where('url', '=', $this->url)
            ->where('page_id', '!=', $this->id())
            ->where('language_id', '=', $this->language_id)
            ->count();

        $route = $this->router()->getRoutingByUrl($this->url);

        if (0 === $numberOfPages && isset($route['route'][0]) && $route['route'][0] === 'frontend_index') {
            return true;
        }

        throw new ValidationException(translate('This URL is already in use. Please change the URL.'));
    }

    /**
     * Get relative page url.
     *
     * @param bool $withBasePath     Set TRUE to get relative URL with base path (e.g. "/cms" when the configured URL of your installation is "http://my.tld/cms")
     * @param bool $withLanguageCode Set TRUE to get relative URL with language code when multiple languages are in use
     * @param bool $forStartpage     SET true to get the URL path for the startpage too
     *
     * @return bool
     */
    public function getRelativeUrl(bool $withBasePath = false, bool $withLanguageCode = false, bool $forStartpage = false): string
    {
        $relativeUrl = '';

        if ($withBasePath) {
            $relativeUrl .= parse_url($this->config()->getUrl(), PHP_URL_PATH);
        }

        $numberOfLanguages = count($this->config()->get('app')->get('languages'));
        if ($withLanguageCode && $numberOfLanguages > 1) {
            $relativeUrl .= '/'.$this->language()->fetch()->code.'/';
        }

        if ($forStartpage || !$this->is_startpage) {
            $relativeUrl .= $this->url;
        }

        return normalize_url($relativeUrl);
    }

    /**
     * Get parent page.
     *
     * @return PageModel|bool
     */
    public function getParentPage()
    {
        $navitem = $this->getMainNavitem();
        if ($navitem) {
            $parentNavitem = $navitem->parentNavitem()->fetch();
            if ($parentNavitem) {
                return $parentNavitem->page()->fetch();
            }
        }

        return false;
    }

    /**
     * Get child pages.
     *
     * @return EntityCollection
     */
    public function getChildPages(): EntityCollection
    {
        $navitem = $this->getMainNavitem();
        $childPages = [];
        if ($navitem) {
            $childNavitems = $navitem->childNavitems()->fetchAll();
            $childPages = $childNavitems->map(function ($childNavitem) {
                return $childNavitem->page()->fetch();
            });
        }

        return new EntityCollection($childPages);
    }

    /**
     * Get parent pages.
     *
     * @return EntityCollection
     */
    public function getParentPages(): EntityCollection
    {
        $parentPages = $this->app()->getService('page')->getParentPages($this);

        return new EntityCollection($parentPages);
    }

    /**
     * Get repository to fetch language.
     *
     * @return Repository
     */
    public function language()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\LanguageModel', 'language_id');
    }

    /**
     * Get repository to fetch navitems.
     *
     * @return Repository
     */
    public function navitems()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\NavitemModel', 'page_id');
    }

    /**
     * Get main navitem.
     *
     * @return NavitemModel
     */
    public function getMainNavitem()
    {
        return $this->navitems()->where('navigation_id', '=', 1)->fetch();
    }

    /**
     * Update modified when and save page.
     *
     * @param bool $preventCacheClearing
     *
     * @return self
     */
    public function updateModifiedWhen(bool $preventCacheClearing = false): self
    {
        $this->modified_when = time();
        $this->save($preventCacheClearing);

        return $this;
    }

    /**
     * Save page.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        if ($this->exists('role_ids')) {
            $this->only_logged_in_users = true;
        }

        if ($this->custom_slug && $this->custom_slug !== $this->slug) {
            $this->has_custom_slug = true;
            $this->slug = slugify($this->custom_slug);
        } elseif (!$this->has_custom_slug || '' === $this->custom_slug) {
            $this->slug = slugify($this->title);
        }

        if (!$this->created_when) {
            $this->created_when = time();
        }

        $this->modified_when = time();

        $result = parent::save($preventCacheClearing);

        $this->getChildPages()->each(function ($childPage) {
            $childPage->save();
        });

        if ($result) {
            if ($this->isNew) {
                $navitem = NavitemModel::create([
                        'navigation_id' => 1,
                        'page_id' => $this->id(),
                        'title' => $this->title,
                        'language_id' => $this->language_id,
                ]);
            } else {
                $navitem = NavitemModel::repo()
                    ->where('page_id', '=', $this->id())
                    ->where('navigation_id', '=', 1)
                    ->fetch();

                if (null !== $this->is_visible) {
                    $navitem->is_active = $this->is_visible;
                }

                if ($this->navigation_title) {
                    $navitem->title = $this->navigation_title;
                } else {
                    $navitem->title = $this->title;
                }
            }

            if (isset($this->parent_navitem_id)) {
                $navitem->parent_navitem_id = $this->parent_navitem_id ?: null;
                $navitem->save($preventCacheClearing);
            }

            if (is_array($this->role_ids)) {
                // Delete old page roles
                PageRoleModel::deleteAllByColumn('page_id', $this->id());

                // Create new page roles
                foreach ($this->role_ids as $role_id) {
                    PageRoleModel::create([
                            'page_id' => $this->id(),
                            'role_id' => $role_id,
                        ])
                        ->save($preventCacheClearing);
                }
            }
        }

        return $result;
    }

    /**
     * Save page.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function saveUrl(bool $preventCacheClearing = false): bool
    {
        $parentPages = $this->getParentPages()->reverse();
        $this->url = '/';
        if ($parentPages->count() > 0) {
            $this->url .= $parentPages->implode(function ($page) {
                return $page->slug;
            }, '/').'/';
        }
        $this->url .= $this->slug;

        return parent::save($preventCacheClearing);
    }

    /**
     * Validate page.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->betweenLength(3, 100)
            ->callback(function ($title, PageModel $page) {
                $result = 0 === PageModel::repo()
                    ->where('title', '=', $title)
                    ->where('language_id', '=', $page->language_id)
                    ->where('page_id', '!=', $page->id())
                    ->count();

                return (bool) $result;
            }, '{0} has to be unique', [$this])
            ->set('title', 'Title');

        return (bool) $validator->validate();
    }

    /**
     * Delete page.
     *
     * @return bool
     */
    public function delete()
    {
        // Delete sub pages
        $navitem = $this->navitems()->where('navigation_id', '=', 1)->fetch();
        if ($navitem) {
            $childNavitems = $navitem->childNavitems()->fetchAll();
            foreach ($childNavitems as $childNavitems) {
                $subpage = $childNavitems->page()->fetch();
                if ($subpage) {
                    $subpage->delete();
                }
            }
        }

        // Delete navigation items
        $navitems = $this->navitems()->fetchAll();
        foreach ($navitems as $navitem) {
            $navitem->delete();
        }

        // Delete sections
        $sections = $this->sections()->fetchAll();
        $sections->each(function ($section) {
            $section->delete();
        });

        // Delete page roles
        PageRoleModel::deleteAllByColumn('page_id', $this->id());

        return parent::delete();
    }

    /**
     * Toggle activation.
     *
     * @return self
     */
    public function toggleActivation(): self
    {
        if ($this->is_active) {
            $this->is_active = false;
        } else {
            $this->is_active = true;
        }

        return $this;
    }

    /**
     * Get roles.
     *
     * @return EntityCollection
     */
    public function getRoles(): EntityCollection
    {
        return $this->roles()->fetchAll();
    }

    /**
     * Render frontend output based on the referenced page module.
     *
     * @param FrontendView $view
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function render(FrontendView $view)
    {
        // Get all sections
        $sections = $this->sections()
            ->orderByAsc('position')
            ->where('block_id', 'IS NOT', null)
            ->where('is_active', '=', true)
            ->fetchAll();

        // Set page-specific meta data
        if ($this->description) {
            $this->engine()->addMetaTagProperties([
                'name' => 'description',
                'content' => $this->description, ], 'description');
        }

        if ($this->keywords) {
            $this->engine()->addMetaTagProperties([
                'name' => 'keywords',
                'content' => $this->keywords, ], 'keywords');
        }

        $view->setTitle($this->title);

        $output = '';
        foreach ($sections as $section) {
            // Render section
            $content = $section->render($view);

            // Get block
            $block = $section->block()->fetch();

            // Render section content
            $content = $view->renderTemplate('frontend/section-content', [
                    'content' => $content,
                    'section' => $section->setReadOnly(),
                    'page' => $this->setReadOnly(),
                    'block' => $block->setReadOnly(),
                ]).PHP_EOL;

            // Add content to output
            $output .= $content;

            // Add content to the block
            if ($block) {
                $view->engine()->addContentToBlock('sections', $content);
                $view->engine()->addContentToBlock('section_'.$block->block_key, $content);
            }
        }

        // Check whether module URL not exist or is routed
        if (!$this->app()->exists('module_url_routed') || $this->app()->get('module_url_routed')) {
            return $output;
        }
        throw new NotFoundException();
    }

    /**
     * Get repository to fetch roles.
     *
     * @return Repository
     */
    public function roles()
    {
        return $this->hasManyThrough('\\Neoflow\\CMS\\Model\\RoleModel', '\\Neoflow\\CMS\\Model\\PageRoleModel', 'page_id', 'role_id');
    }
}
