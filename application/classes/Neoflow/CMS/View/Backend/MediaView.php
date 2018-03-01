<?php

namespace Neoflow\CMS\View\Backend;

use Neoflow\CMS\View\BackendView;

class MediaView extends BackendView
{
    public function __construct()
    {
        parent::__construct();

        $this->engine()->addJavascript('
            var mediaTranslation = {
                "Preview": "'.translate('Preview').'",
                "The file cannot be previewed": "'.translate('The file cannot be previewed').'",
            };', 'head');

        $this->engine()->addJavascriptUrl($this->config()->getUrl('/statics/backend/media.js'));
    }
}
