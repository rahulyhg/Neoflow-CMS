<?php

namespace Neoflow\Module\CodeMirror;

use Neoflow\CMS\Model\ModuleModel;
use Neoflow\Module\Code\Service as CodeService;
use RuntimeException;

class Service extends CodeService
{
    /**
     * @var array
     */
    protected $options = [
        'lineNumbers' => true,
        'styleActiveLine' => true,
        'matchBrackets' => true,
        'mode' => 'text/x-php',
        'indentUnit' => 4,
        'indentWithTabs' => true,
    ];

    /**
     * @var ModuleModel
     */
    protected $module;

    /**
     * Constructor.
     *
     * @param ModuleModel $module
     */
    public function __construct(ModuleModel $module)
    {
        $this->module = $module;
    }

    /**
     * Render editor.
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
        $options = array_merge($this->options, $options);

        $this->engine()
            ->addStylesheetUrl($this->module->getUrl('statics/codemirror/lib/codemirror.css'))
            ->addJavascriptUrl($this->module->getUrl('statics/codemirror/lib/codemirror.js'))
            ->addJavascriptUrl($this->module->getUrl('statics/codemirror/mode/meta.js'))
            ->addJavascriptUrl($this->module->getUrl('statics/custom-meta.js'))
            ->addJavascriptUrl($this->module->getUrl('statics/codemirror/addon/mode/loadmode.js'))
            ->addJavascript('
                    (function() {
                        CodeMirror.modeURL = "'.$this->module->getUrl('statics/codemirror/mode/%N/%N.js').'";
                        var codeMirrorTextarea = CodeMirror.fromTextArea(document.getElementsByName("'.$name.'")[0], {
                            '.convert_php2js($options, false).'
                        });

                        codeMirrorTextarea.setSize("100%", "'.$height.'");

                        var info = CodeMirror.findModeByMIME("'.$options['mode'].'");
                        if (info) {
                            CodeMirror.autoLoadMode(codeMirrorTextarea, info.mode);
                        } else {
                            console.warn("Could not find a mode corresponding to '.$options['mode'].'.");
                        }
                    })();
                    ');

        return parent::renderEditor($name, $id, $content, $height, $options);
    }
}
