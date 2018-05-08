<?php

namespace Neoflow\Framework\Core;

use Neoflow\Framework\AppTrait;
use Neoflow\Framework\Common\Container;
use OutOfRangeException;
use RuntimeException;
use function normalize_path;

abstract class AbstractView extends Container
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Constructor.
     *
     * @param array $data
     * @param bool  $isReadOnly
     * @param bool  $isMultiDimensional
     * @param string parent
     */
    public function __construct(array $data = [], $isReadOnly = false, $isMultiDimensional = false, $parent = '')
    {
        parent::__construct($data, $isReadOnly, $isMultiDimensional, $parent);

        // Preset data
        $this->set('viewDirectories', []);
        $this->set('templateDirectories', []);

        // Set template and view directories of application
        $this
            ->addViewDirectory($this->config()->getApplicationPath('/views'))
            ->addTemplateDirectory($this->config()->getApplicationPath('/templates'));

        $this
            ->logger()->info('View created', [
            'Type' => $this->getReflection()->getShortName(),
        ]);
    }

    /**
     * Get execution time in seconds.
     *
     * @return float
     */
    public function getExecutionTime(): float
    {
        return $this->app()->getExecutionTime();
    }

    /**
     * Get theme url.
     *
     * @param string $additionalUrlPath Additional URL path
     *
     * @return string
     */
    public function getThemeUrl(string $additionalUrlPath = ''): string
    {
        return $this->config()->getUrl('/theme/'.$additionalUrlPath);
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
        return $this->config()->getPath('/theme/'.$uri);
    }

    /**
     * Render view file to html output.
     *
     * @param string $viewFile   View file name
     * @param array  $parameters Parameters for the view content
     * @param string $blockKey   Block key
     *
     * @return string
     *
     * @throws OutOfRangeException
     */
    public function renderView(string $viewFile, array $parameters = [], string $blockKey = 'view'): string
    {
        $parameters['view'] = $this;
        $parameters['app'] = $this->app();

        $viewFilePath = $this->getFilePath($viewFile, $this->viewDirectories);
        if (is_file($viewFilePath)) {
            $content = $this->engine()->renderFile($viewFilePath, $parameters);
            $this->engine()->addContentToBlock($blockKey, $content);

            $this->logger()->debug('View rendered', [
                'File' => $viewFilePath,
            ]);

            return $content;
        }
        throw new OutOfRangeException('View file "'.$viewFile.'" not found');
    }

    /**
     * Render template file to html output.
     *
     * @param string $templateFile Template file name
     * @param array parameters Parameters for the template content
     *
     * @return string
     *
     * @throws OutOfRangeException
     */
    public function renderTemplate(string $templateFile, array $parameters = []): string
    {
        $parameters['view'] = $this;

        $templateFilePath = $this->getFilePath($templateFile, $this->templateDirectories);
        if (is_file($templateFilePath)) {
            $output = $this->engine()->renderFile($templateFilePath, $parameters);

            $this->logger()->debug('Template rendered', [
                'File' => normalize_path($templateFilePath),
            ]);

            return $output;
        }
        throw new OutOfRangeException('Template file "'.$templateFile.'" not found');
    }

    /**
     * Get file path from a list of directories.
     *
     * @param string $file        View or template file name
     * @param array  $directories View or template directories
     *
     * @return string
     */
    protected function getFilePath(string $file, array $directories): string
    {
        if (!is_file($file)) {
            $cacheKey = sha1($file.':'.implode('|', $directories));
            if ($this->cache()->exists($cacheKey)) {
                return $this->cache()->fetch($cacheKey);
            } else {
                foreach ($directories as $directory) {
                    $filePaths = array_map(function ($extension) use ($directory, $file) {
                        return $directory.DIRECTORY_SEPARATOR.$file.$extension;
                    }, ['', '.php', '.html', '.htm']);

                    foreach ($filePaths as $filePath) {
                        if (is_file($filePath)) {
                            $this->cache()->store($cacheKey, $filePath, 0, ['cms_core', 'cms_view', 'cms_views', 'cms_templates']);

                            return $filePath;
                        }
                    }
                }
            }
        }

        return $file;
    }

    /**
     * Render theme to html output.
     *
     * @param string $themeFile
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function renderTheme(string $themeFile = 'index'): string
    {
        $themeFilePaths = [
            $this->getThemePath(DIRECTORY_SEPARATOR.$themeFile),
            $this->getThemePath(DIRECTORY_SEPARATOR.$themeFile.'.php'),
            $this->getThemePath(DIRECTORY_SEPARATOR.$themeFile.'.html'),
        ];

        $parameters = [
            'view' => $this,
        ];

        // Render theme file and get output
        foreach ($themeFilePaths as $themeFilePath) {
            if (is_file($themeFilePath)) {
                ob_start();

                $output = $this->engine()->renderFile($themeFilePath, $parameters);

                $this->logger()->debug('Theme rendered', [
                    'File' => $themeFile,
                ]);

                return $output;
            }
        }

        throw new RuntimeException('Theme file not found');
    }

    /**
     * Add view directory.
     *
     * @param string $viewDirectory
     *
     * @return self
     */
    protected function addViewDirectory(string $viewDirectory): self
    {
        if (is_dir($viewDirectory)) {
            $viewDirectories = $this->get('viewDirectories');
            $viewDirectories[] = normalize_path($viewDirectory);
            $this->set('viewDirectories', $viewDirectories);
        }

        return $this;
    }

    /**
     * Add template directory.
     *
     * @param string $templateDirectory
     *
     * @return self
     */
    protected function addTemplateDirectory(string $templateDirectory): self
    {
        if (is_dir($templateDirectory)) {
            $templateDirectories = $this->get('templateDirectories');
            $templateDirectories[] = normalize_path($templateDirectory);
            $this->set('templateDirectories', $templateDirectories);
        }

        return $this;
    }
}
