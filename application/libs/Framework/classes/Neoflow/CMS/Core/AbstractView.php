<?php

namespace Neoflow\CMS\Core;

use Neoflow\CMS\AppTrait;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\Framework\Core\AbstractView as FrameworkAbstractView;
use RuntimeException;

abstract class AbstractView extends FrameworkAbstractView
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var ThemeModel
     */
    protected $theme;

    /**
     * Constructor.
     *
     * @throws RuntimeException
     */
    public function __construct()
    {
        if (!$this->theme) {
            throw new RuntimeException('Theme not found');
        }

        if ($this->session()->hasFlash('alerts')) {
            $this->set('alerts', $this->session()->getFlash('alerts'));
        }

        $websiteTitle = $this->settings()->get('website_title', '');
        $this->setWebsiteTitle($websiteTitle);

        $cacheKey = 'directories_'.$this->theme->folder_name;
        if ($this->cache()->exists($cacheKey)) {
            // Fetch template and view file directories from cache
            $viewDirectories = $this->cache()->fetch($cacheKey);
            $this->viewDirectories = $viewDirectories['view'];
            $this->templateDirectories = $viewDirectories['template'];
        } else {
            // Set template and view directories of current theme
            $this
                    ->addViewDirectory($this->getThemePath('/views/'))
                    ->addTemplateDirectory($this->getThemePath('/templates/'));

            // Set template and view directories of each active module
            $this->app()->get('modules')->where('is_active', true)->each(function ($module) {
                $this->addViewDirectory($module->getPath('/views/'));
                $this->addTemplateDirectory($module->getPath('/templates/'));
            });

            // Set template and view directories of application
            $this
                    ->addViewDirectory($this->config()->getApplicationPath('/views/'))
                    ->addTemplateDirectory($this->config()->getApplicationPath('/templates/'));

            // Store template and view file directories to cache
            $viewDirectories = [
                'view' => $this->viewDirectories,
                'template' => $this->templateDirectories,
            ];
            $this->cache()->store($cacheKey, $viewDirectories, 0, ['cms_core', 'cms_view', 'cms_views', 'cms_templates']);
        }

        $this->logger()->info('View created', [
            'Type' => $this->getReflection()->getShortName(),
        ]);
    }

    /**
     * Get theme URL.
     *
     * @param string $additionalUrlPath Additional URL path
     *
     * @return string
     */
    public function getThemeUrl(string $additionalUrlPath = ''): string
    {
        return $this->theme->getUrl($additionalUrlPath);
    }

    /**
     * Get theme path.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function getThemePath(string $uri = ''): string
    {
        return $this->theme->getPath($uri);
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->set('title', $title);

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->get('title', '');
    }

    /**
     * Set website title.
     *
     * @param string $websiteTitle
     *
     * @return self
     */
    public function setWebsiteTitle(string $websiteTitle): self
    {
        $this->set('websiteTitle', $websiteTitle);

        return $this;
    }

    /**
     * Get website title.
     *
     * @return string
     */
    public function getWebsiteTitle(): string
    {
        return $this->get('websiteTitle', '');
    }

    /**
     * Get website URL.
     *
     * @param string $additionalUrlPath Additional URL path
     *
     * @return string
     */
    public function getWebsiteUrl(string $additionalUrlPath = ''): string
    {
        return $this->config()->getUrl($additionalUrlPath);
    }
}
