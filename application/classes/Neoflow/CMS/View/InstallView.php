<?php

namespace Neoflow\CMS\View;

use Neoflow\CMS\Core\AbstractView;
use Neoflow\CMS\Model\ThemeModel;

class InstallView extends AbstractView
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set theme
        $this->theme = new ThemeModel([
            'folder_name' => 'neoflow-backend',
        ]);

        // Set backend-specific meta data
        $this->engine()->addMetaTagProperties([
            'name' => 'robots',
            'content' => 'noindex',
                ], 'robots');

        parent::__construct();
    }

    /**
     * Render alert.
     *
     * @return string
     */
    public function renderAlertTemplate(): string
    {
        if ($this->service('alert')->count() > 0) {
            return $this->renderTemplate('backend/alert', [
                        'alerts' => $this->service('alert')->getAll(),
            ]);
        }

        return '';
    }
}
