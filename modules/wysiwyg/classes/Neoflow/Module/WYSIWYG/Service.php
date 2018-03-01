<?php

namespace Neoflow\Module\WYSIWYG;

use Neoflow\CMS\Core\AbstractService;
use RuntimeException;

class Service extends AbstractService
{
    /**
     * @var string
     */
    protected $templateFile = 'wysiwyg/editor';

    /**
     * Render code editor.
     *
     * @param string $name
     * @param string $id
     * @param string $content
     * @param string $height
     * @param array  $options
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function renderEditor(string $name, string $id, string $content, string $height = '350px', array $options = []): string
    {
        if ($this->app()->get('view')) {
            if ($this->templateFile) {
                return $this->app()->get('view')->renderTemplate($this->templateFile, [
                            'name' => $name,
                            'id' => $id,
                            'content' => $content,
                            'height' => $height,
                            'options' => $options,
                ]);
            }
            throw new RuntimeException('Template file not found');
        }
        throw new RuntimeException('View not found');
    }
}
