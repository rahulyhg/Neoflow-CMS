<?php

namespace Neoflow\Module\WYSIWYG;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Core\AbstractView;
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
     * @param AbstractView $view    View
     * @param string       $name    Editor name (form control / textarea)
     * @param string       $id      Editor id (form control / textarea)
     * @param string       $content Editor content
     * @param string       $height  Editor height
     * @param array        $options Editor options
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function renderEditor(AbstractView $view, string $name, string $id, string $content, string $height = '350px', array $options = []): string
    {
        if ($this->templateFile) {
            return $view->renderTemplate($this->templateFile, [
                'name' => $name,
                'id' => $id,
                'content' => $content,
                'height' => $height,
                'options' => $options,
            ]);
        }
        throw new RuntimeException('Template file not found');
    }
}
