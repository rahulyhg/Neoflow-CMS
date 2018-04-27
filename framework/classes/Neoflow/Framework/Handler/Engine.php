<?php

namespace Neoflow\Framework\Handler;

use InvalidArgumentException;
use Neoflow\Framework\AppTrait;
use OutOfRangeException;
use RuntimeException;

class Engine
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var array
     */
    protected $blocks = [];

    /**
     * @var array
     */
    protected $openBlocks = [];

    /**
     * @var array
     */
    protected $resourceUrls = [
        'css' => [],
        'javascript' => [],
    ];

    /**
     * @var array
     */
    protected $sources = [
        'css' => [],
        'javascript' => [],
    ];

    /**
     * @var array
     */
    protected $tagProperties = [
        'meta' => [],
        'tags' => [],
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->logger()->debug('HTML engine created');
    }

    /**
     * Start buffering block.
     *
     * @param string $key Block key
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function startBlock(string $key): self
    {
        if (in_array($key, $this->openBlocks)) {
            throw new RuntimeException('Block has already started (Key: '.$key.')');
        }
        $this->openBlocks[] = $key;
        ob_start();
        ob_implicit_flush(false);

        return $this;
    }

    /**
     * Stop buffering block.
     *
     * @return self
     *
     * @throws OutOfRangeException
     */
    public function stopBlock(): self
    {
        if (0 === count($this->openBlocks)) {
            throw new OutOfRangeException('Started block not found');
        }

        $key = array_pop($this->openBlocks);

        $this->blocks[$key][] = ob_get_contents();
        ob_end_clean();

        return $this;
    }

    /**
     * Render block content.
     *
     * @param string $key           Block key
     * @param string $preSeparator  Pre content separator
     * @param string $postSeparator Post content separator
     *
     * @return string
     */
    public function renderBlock(string $key, string $preSeparator = '', string $postSeparator = ''): string
    {
        $result = '';
        if ($this->hasBlock($key)) {
            foreach ($this->blocks[$key] as $block) {
                $result .= $preSeparator.$block.$postSeparator;
            }

            return $result;
        }

        return $result;
    }

    /**
     * Get all blocks.
     *
     * @return array
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * Unset all blocks.
     *
     * @return bool
     */
    public function unsetBlocks(): bool
    {
        $this->blocks = [];

        return true;
    }

    /**
     * Get block content.
     *
     * @param string $key Block key
     *
     * @return array
     */
    public function getBlock(string $key): array
    {
        if ($this->hasBlock($key)) {
            return $this->blocks[$key];
        }

        return [];
    }

    /**
     * Check whether block content exists.
     *
     * @param string $key Block key
     *
     * @return bool
     */
    public function hasBlock(string $key): bool
    {
        return isset($this->blocks[$key]);
    }

    /**
     * Set block content.
     *
     * @param string $key     Block key
     * @param string $content Block content
     *
     * @return self
     */
    public function setBlock(string $key, string $content): self
    {
        $this->blocks[$key][] = $content;

        return $this;
    }

    /**
     * Unset block content.
     *
     * @param string $key Block key
     *
     * @return bool
     */
    public function unsetBlock(string $key): bool
    {
        if ($this->hasBlock($key)) {
            unset($this->blocks[$key]);
        }

        return true;
    }

    /**
     * Add content to block.
     *
     * @param string $key     Block key
     * @param string $content Block content
     *
     * @return self
     */
    public function addContentToBlock(string $key, string $content): self
    {
        if (isset($this->blocks[$key])) {
            $this->blocks[$key][] = $content;
        } else {
            $this->setBlock($key, $content);
        }

        return $this;
    }

    /**
     * Add parameters.
     *
     * @param array $parameters
     *
     * @return self
     */
    protected function addParameters(array $parameters): self
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * Render file to output html.
     *
     * @param string $filePath
     * @param array  $parameters
     *
     * @return string
     *
     * @throws OutOfRangeException
     */
    public function renderFile(string $filePath, array $parameters = []): string
    {
        if (is_file($filePath)) {
            $parameters['engine'] = $this;
            $output = call_user_func(function () use ($filePath, $parameters) {
                extract($parameters);
                unset($parameters);
                ob_start();
                ob_implicit_flush(false);
                include $filePath;
                $output = ob_get_contents();
                ob_end_clean();

                return $output;
            });

            // Search and replace placeholders
            foreach ($parameters as $key => $value) {
                if (is_string($value) || is_integer($value)) {
                    $output = str_replace('['.$key.']', $value, $output);
                }
            }

            return $output;
        }
        throw new OutOfRangeException('File "'.$filePath.'" not found');
    }

    /**
     * Add resource URL.
     *
     * @param string $url  Ressource URL
     * @param string $type Resource type (css or javascript)
     * @param string $key  Group key
     *
     * @return self
     *
     * @throw InvalidArgumentException
     */
    protected function addResourceUrl(string $url, string $type, string $key = 'default'): self
    {
        if ('css' === $type || 'javascript' === $type) {
            if (!isset($this->resourceUrls[$type][$key]) || !in_array($url, $this->resourceUrls[$type][$key])) {
                $this->resourceUrls[$type][$key][] = $url;
            }

            return $this;
        }
        throw new InvalidArgumentException('Type of resource has to be "css" or "javascript"');
    }

    /**
     * Add CSS file URL.
     *
     * @param string $url CSS file URL
     * @param string $key Group key
     *
     * @return self
     */
    public function addCssUrl(string $url, string $key = 'default'): self
    {
        return $this->addResourceUrl($url, 'css', $key);
    }

    /**
     * Add stylesheet URL.
     *
     * @param string $url Stylesheet URL
     * @param string $key Group key
     *
     * @return self
     */
    public function addStylesheetUrl(string $url, string $key = 'default'): self
    {
        return $this->addCssUrl($url, $key);
    }

    /**
     * Add Javascript URL.
     *
     * @param string $url Javascript URL
     * @param string $key Group key
     *
     * @return self
     */
    public function addJavascriptUrl(string $url, string $key = 'default'): self
    {
        return $this->addResourceUrl($url, 'javascript', $key);
    }

    /**
     * Get resource URLs.
     *
     * @param string $type Resource type (css or javascript)
     * @param string $key  Group key
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getResourceUrls(string $type, string $key = 'default'): array
    {
        if ('javascript' === $type || 'css' === $type) {
            if (isset($this->resourceUrls[$type][$key])) {
                return $this->resourceUrls[$type][$key];
            }

            return [];
        }
        throw new InvalidArgumentException('Type of resource has to be "javascript" or "css"');
    }

    /**
     * Get Javascript URLs.
     *
     * @param string $key Group key
     *
     * @return array
     */
    public function getJavascriptUrls(string $key = 'default'): array
    {
        return $this->getResourceUrls('javascript', $key);
    }

    /**
     * Get CSS URLs.
     *
     * @param string $key Group key
     *
     * @return array
     */
    public function getCssUrls(string $key = 'default'): array
    {
        return $this->getResourceUrls('css', $key);
    }

    /**
     * Get stylesheet URLs.
     *
     * @param string $key Group key
     *
     * @return array
     */
    public function getStylesheetUrls(string $key = 'default'): array
    {
        return $this->getCssUrls($key);
    }

    /**
     * Render resource URLs.
     *
     * @param string $type     Resource type (css or javascript)
     * @param string $template Resource template
     * @param string $key      Group key
     *
     * @return string
     */
    protected function renderResourceUrls(string $type, string $template, string $key = 'default'): string
    {
        $output = '';
        $urls = $this->getResourceUrls($type, $key);
        foreach ($urls as $url) {
            $output .= sprintf($template, $url).PHP_EOL;
        }

        return $output;
    }

    /**
     * Render Javascript URLs.
     *
     * @param string $key Group key
     *
     * @return string
     */
    public function renderJavascriptUrls(string $key = 'default'): string
    {
        return $this->renderResourceUrls('javascript', '<script src="%s"></script>', $key);
    }

    /**
     * Render CSS URLs.
     *
     * @param string $key Group key
     *
     * @return string
     */
    public function renderCssUrls(string $key = 'default'): string
    {
        return $this->renderResourceUrls('css', '<link href="%s" rel="stylesheet" type="text/css" />', $key);
    }

    /**
     * Render stylesheets urls.
     *
     * @param string $key Group key
     *
     * @return string
     */
    public function renderStylesheetUrl(string $key = 'default'): string
    {
        return $this->renderCssUrls($key);
    }

    /**
     * Add source.
     *
     * @param string $source Source code
     * @param string $type   Source type (css or javascript)
     * @param string $key    Group key
     *
     * @return self
     *
     * @throw InvalidArgumentException
     */
    protected function addSource(string $source, string $type, string $key = 'default'): self
    {
        if ('javascript' === $type || 'css' === $type) {
            $this->sources[$type][$key][] = $source;

            return $this;
        }
        throw new InvalidArgumentException('Type of resource has to be "javascript" or "css"');
    }

    /**
     * Add Javascript source.
     *
     * @param string $source Javascript source
     * @param string $key    Group key
     *
     * @return self
     */
    public function addJavascript(string $source, string $key = 'default'): self
    {
        return $this->addSource($source, 'javascript', $key);
    }

    /**
     * Add CSS source.
     *
     * @param string $source CSS source
     * @param string $key    Group key
     *
     * @return self
     */
    public function addCss(string $source, string $key = 'default'): self
    {
        return $this->addSource($source, 'css', $key);
    }

    /**
     * Get sources.
     *
     * @param string $type Source type (css or javascript)
     * @param string $key  Group key
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getSource(string $type, string $key = 'default'): array
    {
        if ('javascript' === $type || 'css' === $type) {
            if (isset($this->sources[$type][$key])) {
                return $this->sources[$type][$key];
            }

            return [];
        }
        throw new InvalidArgumentException('Type of resource has to be "javascript" or "css"');
    }

    /**
     * Get Javascript source.
     *
     * @param string $key Group key
     *
     * @return array
     */
    public function getJavascript(string $key = 'default'): array
    {
        return $this->getSource('javascript', $key);
    }

    /**
     * Get CSS source.
     *
     * @param string $key Group key
     *
     * @return array
     */
    public function getCss(string $key = 'css'): array
    {
        return $this->getSource('css', $key);
    }

    /**
     * Render source.
     *
     * @param string $type     Source code type (css or javascript)
     * @param string $template Source template
     * @param string $key      Group key
     *
     * @return string
     */
    protected function renderSource(string $type, string $template, string $key = 'default'): string
    {
        $source = implode(PHP_EOL, $this->getSource($type, $key));
        if ($source) {
            return sprintf($template, $source).PHP_EOL;
        }

        return '';
    }

    /**
     * Render Javascript source.
     *
     * @param string $key Group key
     *
     * @return string
     */
    public function renderJavascript(string $key = 'default'): string
    {
        return $this->renderSource('javascript', '<script>'.PHP_EOL.'%s'.PHP_EOL.'</script>', $key);
    }

    /**
     * Render CSS source.
     *
     * @param string $key Group key
     *
     * @return string
     */
    public function renderCss(string $key = 'default'): string
    {
        return $this->renderSource('css', '<style>'.PHP_EOL.'%s'.PHP_EOL.'</style>', $key);
    }

    /**
     * Add meta tag properties.
     *
     * @param array  $properties Meta tag properties
     * @param string $name       Tag name
     * @param string $key        Group key
     *
     * @return self
     */
    public function addMetaTagProperties(array $properties, string $name = '', string $key = 'default'): self
    {
        return $this->addTagProperties($properties, $name, 'meta', $key);
    }

    /**
     * Add link tag properties.
     *
     * @param array  $properties Link tag properties
     * @param string $name       Tag name
     * @param string $key        Group key
     *
     * @return self
     */
    public function addLinkTagProperties(array $properties, string $name = '', string $key = 'default'): self
    {
        return $this->addTagProperties($properties, $name, 'link', $key);
    }

    /**
     * Add tag properties.
     *
     * @param array  $properties Tag properties
     * @param string $name       Tag name
     * @param string $type       Tag type (meta or link)
     * @param string $key        Group key
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function addTagProperties(array $properties, string $name, string $type, string $key = 'default'): self
    {
        if ('meta' === $type || 'link' === $type) {
            if (!isset($this->tagProperties[$type][$key])) {
                $this->tagProperties[$type][$key] = [];
            }

            if ('' === $name) {
                $name = sha1(implode(';', $properties));
            }

            $this->tagProperties[$type][$key][$name] = $properties;

            return $this;
        }
        throw new InvalidArgumentException('Type of tag has to be "meta" or "link"');
    }

    /**
     * Get tag properties.
     *
     * @param string $type Tag type (meta or link)
     * @param string $key  Group key
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getTagProperties(string $type, string $key = 'default'): array
    {
        if ('meta' === $type || 'link' === $type) {
            if (isset($this->tagProperties[$type][$key])) {
                return $this->tagProperties[$type][$key];
            }

            return [];
        }
        throw new InvalidArgumentException('Type of tag has to be "meta" or "link"');
    }

    /**
     * Get meta tag properties.
     *
     * @param string $key Group key
     *
     * @return array
     */
    public function getMetaTagProperties(string $key = 'default'): array
    {
        return $this->getTagProperties('meta', $key);
    }

    /**
     * Get link tag properties.
     *
     * @param string $key Group key
     *
     * @return array
     */
    public function getLinkTagProperties(string $key = 'default'): array
    {
        return $this->getTagProperties('link', $key);
    }

    /**
     * Render tags properties.
     *
     * @param string $type     Tag type (meta or link)
     * @param string $template Tag template
     * @param string $key      Group key
     *
     * @return string
     */
    protected function renderTagProperties(string $type, string $template, string $key = 'default'): string
    {
        $output = '';
        $tags = $this->getTagProperties($type, $key);
        foreach ($tags as $tag) {
            $properties = array_filter(array_map(function ($value, $key) {
                if (is_string($key)) {
                    return $key.'="'.$value.'"';
                }
            }, $tag, array_keys($tag)));
            $output .= sprintf($template, implode(' ', $properties)).PHP_EOL;
        }

        return $output;
    }

    /**
     * Render meta tag properties.
     *
     * @param string $key Group key
     *
     * @return string
     */
    public function renderMetaTagProperties(string $key = 'default'): string
    {
        return $this->renderTagProperties('meta', '<meta %s />', $key);
    }

    /**
     * Render link tag properties.
     *
     * @param string $key Group key
     *
     * @return string
     */
    public function renderLinkTagProperties(string $key = 'default'): string
    {
        return $this->renderTagProperties('link', '<link %s />', $key);
    }
}
