<?php

namespace Neoflow\Module\Code;

use ErrorException;
use Neoflow\CMS\Core\AbstractService;
use RuntimeException;

class Service extends AbstractService
{
    /**
     * @var string
     */
    protected $templateFile = 'code/editor';

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
        throw new RuntimeException('View not found. Check that the view is set before render');
    }

    /**
     * Execute code.
     *
     * @param string $code       Executable PHP code
     * @param array  $parameters Parameters for PHP code
     *
     * @return mixed
     */
    public function executeCode(string $code, array $parameters = [])
    {
        set_error_handler(function (int $code, string $message, string $file, int $line) {
            throw new ErrorException($message, $code, 1, $file, $line);
        }, E_ALL);

        // Execute code
        $result = @eval('return (function() use ($parameters) {extract($parameters);'.$code.'})();');

        // Reset error handler
        $this->app()->registerErrorHandler();

        return $result;
    }
}
