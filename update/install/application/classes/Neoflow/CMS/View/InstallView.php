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
     * @param AbstractAlert $alert
     *
     * @return string
     */
    public function renderAlertTemplate(): string
    {
        if ($this->hasAlerts()) {
            return $this->renderTemplate('backend/alert', [
                    'alerts' => $this->getAlerts(),
            ]);
        }

        return '';
    }
}
