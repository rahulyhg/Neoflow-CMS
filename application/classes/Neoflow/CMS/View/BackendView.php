<?php

namespace Neoflow\CMS\View;

use Neoflow\CMS\Core\AbstractView;
use Neoflow\Alert\AbstractAlert;

class BackendView extends AbstractView
{
    /**
     * @var string
     */
    protected $subtitle = '';

    /**
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * @var string
     */
    protected $backUrl = '';

    /**
     * @var string
     */
    protected $previewUrl = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set theme
        $this->theme = $this
            ->settings()
            ->getBackendTheme();

        // Set backend-specific meta data
        $this->engine()->addMetaTagProperties([
            'name' => 'robots',
            'content' => 'noindex',
        ]);

        parent::__construct();
    }

    /**
     * Set subtitle.
     *
     * @param string $subtitle
     *
     * @return self
     */
    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Add breadcrumb.
     *
     * @param string $title
     * @param string $url
     *
     * return self
     */
    public function addBreadcrumb(string $title, string $url = ''): self
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'url' => $url,
        ];

        return $this;
    }

    /**
     * Get breadcrumbs.
     *
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Set back route as back url.
     *
     * @param string $routeKey
     * @param array  $args
     *
     * @return self
     */
    public function setBackRoute(string $routeKey, array $args = []): self
    {
        $backUrl = generate_url($routeKey, $args);

        return $this->setBackUrl($backUrl);
    }

    /**
     * Get back url.
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->backUrl;
    }

    /**
     * Set back url.
     *
     * @param string $backUrl
     *
     * @return self
     */
    public function setBackUrl(string $backUrl): self
    {
        $this->backUrl = $backUrl;

        return $this;
    }

    /**
     * Set preview route as preview url.
     *
     * @param string $routeKey
     * @param array  $args
     *
     * @return self
     */
    public function setPreviewRoute(string $routeKey, array $args = []): self
    {
        $previewUrl = generate_url($routeKey, $args);

        return $this->setPreviewUrl($previewUrl);
    }

    /**
     * Get preview url.
     *
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return $this->previewUrl;
    }

    /**
     * Set preview url.
     *
     * @param string $previewUrl
     *
     * @return self
     */
    public function setPreviewUrl(string $previewUrl): self
    {
        $this->previewUrl = $previewUrl;

        return $this;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * Render alert.
     *
     * @param AbstractAlert $alert
     *
     * @return string
     */
    public function renderAlertTemplate(): string
    {
        if ($this->hasAlerts()) {
            return $this->renderTemplate('backend/alert', array(
                    'alerts' => $this->getAlerts(),
            ));
        }

        return '';
    }
}
