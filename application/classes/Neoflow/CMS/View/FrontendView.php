<?php
namespace Neoflow\CMS\View;

use Neoflow\CMS\Core\AbstractView;
use Neoflow\CMS\Model\BlockModel;
use Neoflow\CMS\Model\NavigationModel;
use RuntimeException;

class FrontendView extends AbstractView
{

    /**
     * @var array
     */
    protected $sectionContent = [
        'sequential' => [],
        'grouped' => [],
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set theme
        $this->theme = $this
            ->settings()
            ->getFrontendTheme();

        parent::__construct();
    }

    /**
     * Render view file to html output.
     *
     * @param string $viewFile   View file name
     * @param array  $parameters Parameters for the view content
     * @param string $blockKey   Block key
     *
     * @return string
     */
    public function renderView(string $viewFile, array $parameters = [], string $blockKey = 'view'): string
    {
        if ('page' === $viewFile) {
            if ($this->app()->get('page')) {
                return $this->app()->get('page')->render($this);
            }
            throw new RuntimeException('Cannot render page as view');
        }

        if ('view' === $blockKey) {
            $block = BlockModel::repo()->fetch();
            $blockKey = 'section_' . $block->block_key;

            $content = parent::renderView($viewFile, $parameters, 'frontend-view');

            $content = $this->renderTemplate('frontend/static-content', [
                    'content' => $content,
                    'block' => $block->setReadOnly(),
                ]) . PHP_EOL;

            $this->engine()->unsetBlock('frontend-view');

            $this->engine()->setBlock($block->block_key, $content);
            $this->engine()->setBlock('sections', $content);

            return $content;
        }

        return parent::renderView($viewFile, $parameters, $blockKey);
    }

    /**
     * Get content of page sections.
     *
     * @param string $blockKey
     *
     * @return array
     */
    public function getBlock(string $blockKey = null): array
    {
        if ($blockKey) {
            return $this->engine()->getBlock('section_' . $blockKey);
        }

        return $this->engine()->getBlock('sections');
    }

    /**
     * Render section content.

     *
     * @param string $preSeparator  Pre content separator
     * @param string $postSeparator Post content separator
     *
     * @return string
     */
    public function renderSections(string $preSeparator = '', string $postSeparator = ''): string
    {
        return $this->engine()->renderBlock('sections', $preSeparator, $postSeparator);
    }

    /**
     * Render section content by block.
     *
     * @param string $blockKey      Block key
     * @param string $preSeparator  Pre content separator
     * @param string $postSeparator Post content separator
     *
     * @return string
     */
    public function renderSectionsByBlock(string $blockKey, string $preSeparator = '', string $postSeparator = ''): string
    {
        return $this->engine()->renderBlock('section_' . $blockKey, $preSeparator, $postSeparator);
    }

    /**
     * Get navigation.
     *
     * @param string $navigationKey
     * @param int    $startLevel
     * @param int    $maxLevel
     *
     * @return array
     */
    public function getNavigation(string $navigationKey = 'page-tree', int $startLevel = 0, int $maxLevel = 5): array
    {
        $navigationTree = [];
        $cacheKey = $this->generateCacheKey($navigationKey . $startLevel . $maxLevel);

        if ($this->cache()->exists($cacheKey)) {
            return $this->cache()->fetch($cacheKey);
        } else {
            $navigation = NavigationModel::findByColumn('navigation_key', $navigationKey);
            if ($navigation) {
                $navigationTree = $navigation->getNavigationTree($startLevel, $maxLevel, true, $this->translator()->getActiveLanguage()->id());

                // Store navigation tree to cache
                $this->cache()->store($cacheKey, $navigationTree, 0, ['system-configurations', 'navigation-trees']);
            }
        }

        return $navigationTree;
    }

    /**
     * Generate cache key based random salt value, current page and authenticated user.
     *
     * @param string $salt
     *
     * @return string
     */
    protected function generateCacheKey($salt)
    {
        $authenticedUser = $this->app()->getService('auth')->getUser();
        $currentPage = $this->app()->get('page');

        return sha1($salt . ($authenticedUser ? $authenticedUser->role_id : 'anonymous') . ($currentPage ? $currentPage->page_id : 'static'));
    }

    /**
     * Get breadcrumbs based on navigation tree.
     *
     * @param int $startLevel
     * @param int $maxLevel
     *
     * @return array
     */
    public function getBreadcrumbs(int $startLevel = 0, int $maxLevel = 5)
    {
        $breadcrumbs = [];
        $cacheKey = $this->generateCacheKey('breadcrumbs' . $startLevel . $maxLevel);

        if ($this->cache()->exists($cacheKey)) {
            return $this->cache()->fetch($cacheKey);
        } else {
            $navigationTree = $this->getNavigation('page-tree', $startLevel, $maxLevel);

            for ($i = 1; $i <= $maxLevel; ++$i) {
                foreach ($navigationTree as $index => $navTreeItem) {
                    if (in_array($navTreeItem['status'], ['active', 'active-parent'])) {
                        $breadcrumbs[] = $navTreeItem;
                        $navigationTree = $navTreeItem['children'];
                        break;
                    }
                }
            }

            // Store navigation tree to cache
            $this->cache()->store($cacheKey, $breadcrumbs, 0, ['system-configurations', 'navigation-trees']);
        }

        return $breadcrumbs;
    }

    /**
     * Render navigation.
     *
     * @param string $navigationKey
     * @param int    $startLevel
     * @param int    $maxLevel
     * @param string $templateFile
     *
     * @return string
     */
    public function renderNavigation(string $navigationKey, int $startLevel = 0, int $maxLevel = 5, string $templateFile = 'frontend/navigation'): string
    {
        $navigation = $this->getNavigation($navigationKey, $startLevel, $maxLevel);

        return $this->renderTemplate($templateFile, [
                'navigation' => $navigation,
        ]);
    }

    /**
     * Render breadcrumbs.
     *
     * @param int    $startLevel
     * @param int    $maxLevel
     * @param string $templateFile
     *
     * @return string
     */
    public function renderBreadcrumbs(int $startLevel = 0, int $maxLevel = 5, string $templateFile = 'frontend/breadcrumbs'): string
    {
        $breadcrumbs = $this->getBreadcrumbs($startLevel, $maxLevel);

        return $this->renderTemplate($templateFile, [
                'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Render theme to html output.
     *
     * @param string $themeFile
     *
     * @return string
     */
    public function renderTheme(string $themeFile = 'index'): string
    {
        // Add custom frontend JavaScript
        if ($this->settings()->show_custom_js) {
            if ($this->settings()->custom_js) {
                $this->engine()->addJavascript($this->settings()->custom_js);
            }
        }

        // Add custom frontend CSS
        if ($this->settings()->show_custom_css) {
            if ($this->settings()->custom_css) {
                $this->engine()->addCss($this->settings()->custom_css);
            }
        }

        return parent::renderTheme($themeFile);
    }
}
