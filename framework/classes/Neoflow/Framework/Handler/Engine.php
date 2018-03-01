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
     * @throws RuntimeException
     */
    public function startBlock(string $key)
    {
        if (in_array($key, $this->openBlocks)) {
            throw new RuntimeException('Block has already started (Key: '.$key.')');
        }
        $this->openBlocks[] = $key;
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * Stop buffering block.
     *
     * @throws OutOfRangeException
     */
    public function stopBlock()
    {
        if (0 === count($this->openBlocks)) {
            throw new OutOfRangeException('Started block not found');
        }

        $key = array_pop($this->openBlocks);

        $this->blocks[$key][] = ob_get_contents();
        ob_end_clean();
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
    public function renderBlock(string $key, string $preSeparator = '', string $postSeparator = '')
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
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Unset all blocks.
     *
     * @return self
     */
    public function unsetBlocks()
    {
        $this->blocks = [];

        return $this;
    }

    /**
     * Get block content.
     *
     * @param string $key Block key
     *
     * @return array
     */
    public function getBlock(string $key)
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
    public function hasBlock(string $key)
    {
        return isset($this->blocks[$key]);
    }

    /**
     * Set block content.
     *
     * @param string $key     Block key
     * @param string $content Block content
     *
     * @return View
     */
    public function setBlock(string $key, string $content)
    {
        $this->blocks[$key][] = $content;

        return $this;
    }

    /**
     * Unset block content.
     *
     * @param string $key Block key
     *
     * @return View
     */
    public function unsetBlock(string $key)
    {
        if ($this->hasBlock($key)) {
            unset($this->blocks[$key]);
        }

        return $this;
    }

    /**
     * Add content to block.
     *
     * @param string $key     Block key
     * @param string $content Block content
     *
     * @return View
     */
    public function addContentToBlock(string $key, string $content)
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
     */
    protected function addParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Render file to output html.
     *
     * @param string $filePath
     * @param array  $parameters
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function renderFile($filePath, array $parameters = [])
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
     * Add resource url.
     *
     * @param string $url
     * @param string $type
     * @param string $key
     *
     * @return self
     *
     * @throw InvalidArgumentException
     */
    protected function addResourceUrl($url, $type, $key = 'default')
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
     * Add CSS url.
     *
     * @param string $url
     * @param string $key
     *
     * @return self
     */
    public function addCssUrl($url, $key = 'default')
    {
        return $this->addResourceUrl($url, 'css', $key);
    }

    /**
     * Add stylesheet url.
     *
     * @param string $url
     * @param string $key
     *
     * @return self
     */
    public function addStylesheetUrl($url, $key = 'default')
    {
        return $this->addCssUrl($url, $key);
    }

    /**
     * Add Javascript url.
     *
     * @param string $url
     * @param string $key
     *
     * @return self
     */
    public function addJavascriptUrl($url, $key = 'default')
    {
        return $this->addResourceUrl($url, 'javascript', $key);
    }

    /**
     * Get resource urls.
     *
     * @param string $type
     * @param string $key
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getResourceUrls($type, $key = 'default')
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
     * Get Javascript urls.
     *
     * @param string $key
     *
     * @return array
     */
    public function getJavascriptUrls($key = 'default')
    {
        return $this->getResourceUrls('javascript', $key);
    }

    /**
     * Get CSS urls.
     *
     * @param string $key
     *
     * @return array
     */
    public function getCssUrls($key = 'default')
    {
        return $this->getResourceUrls('css', $key);
    }

    /**
     * Get stylesheet urls.
     *
     * @param string $key
     *
     * @return array
     */
    public function getStylesheetUrls($key = 'default')
    {
        return $this->getCssUrls($key);
    }

    /**
     * Render resource urls.
     *
     * @param string $type
     * @param string $template
     * @param string $key
     *
     * @return string
     */
    protected function renderResourceUrls($type, $template, $key = 'default')
    {
        $output = '';
        $urls = $this->getResourceUrls($type, $key);
        foreach ($urls as $url) {
            $output .= sprintf($template, $url).PHP_EOL;
        }

        return $output;
    }

    /**
     * Render Javascript urls.
     *
     * @param string $key
     *
     * @return string
     */
    public function renderJavascriptUrls($key = 'default')
    {
        return $this->renderResourceUrls('javascript', '<script src="%s"></script>', $key);
    }

    /**
     * Render CSS urls.
     *
     * @param string $key
     *
     * @return string
     */
    public function renderCssUrls($key = 'default')
    {
        return $this->renderResourceUrls('css', '<link href="%s" rel="stylesheet" type="text/css" />', $key);
    }

    /**
     * Render stylesheets urls.
     *
     * @param string $key
     *
     * @return string
     */
    public function renderStylesheetUrl($key = 'default')
    {
        return $this->renderCssUrls($key);
    }

    /**
     * Add source.
     *
     * @param string $source
     * @param string $type
     * @param string $key
     *
     * @return self
     *
     * @throw InvalidArgumentException
     */
    protected function addSource($source, $type, $key = 'default')
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
     * @param string $url
     * @param string $key
     * @param bool   $isRelative
     *
     * @return self
     */
    public function addJavascript($source, $key = 'default')
    {
        return $this->addSource($source, 'javascript', $key);
    }

    /**
     * Add CSS source.
     *
     * @param string $source
     * @param string $key
     *
     * @return self
     */
    public function addCss($source, $key = 'default')
    {
        return $this->addSource($source, 'css', $key);
    }

    /**
     * Get sources.
     *
     * @param string $type
     * @param string $key
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getSource($type, $key = 'default')
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
     * @param string $key
     *
     * @return array
     */
    public function getJavascript($key = 'default')
    {
        return $this->getSource('javascript', $key);
    }

    /**
     * Get CSS source.
     *
     * @param string $key
     *
     * @return array
     */
    public function getCss($key = 'css')
    {
        return $this->getSource('css', $key);
    }

    /**
     * Render source.
     *
     * @param string $type
     * @param string $template
     * @param string $key
     *
     * @return string
     */
    protected function renderSource($type, $template, $key = 'default')
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
     * @param string $key
     *
     * @return string
     */
    public function renderJavascript($key = 'default')
    {
        return $this->renderSource('javascript', '<script>'.PHP_EOL.'%s'.PHP_EOL.'</script>', $key);
    }

    /**
     * Render CSS source.
     *
     * @param string $key
     *
     * @return string
     */
    public function renderCss($key = 'default')
    {
        return $this->renderSource('css', '<style>'.PHP_EOL.'%s'.PHP_EOL.'</style>', $key);
    }

    /**
     * Add meta tag properties.
     *
     * @param array  $properties Meta tag properties
     * @param string $name       Unique identifiable name
     * @param string $key        Unique group key
     *
     * @return self
     */
    public function addMetaTagProperties(array $properties, string $name = '', string $key = 'default')
    {
        return $this->addTagProperties($properties, $name, 'meta', $key);
    }

    /**
     * Add link tag properties.
     *
     * @param array  $properties Link tag properties
     * @param string $name       Unique identifiable name
     * @param string $key        Unique group key
     *
     * @return self
     */
    public function addLinkTagProperties(array $properties, string $name = '', string $key = 'default')
    {
        return $this->addTagProperties($properties, $name, 'link', $key);
    }

    /**
     * Add tag properties.
     *
     * @param array  $properties Tag properties
     * @param string $name       Unique identifiable name
     * @param string $type       Type of tag
     * @param string $key        Unique group key
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function addTagProperties(array $properties, string $name, string $type, string $key = 'default')
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
     * @param string $type Type of tag
     * @param string $key  Unique group key
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getTagProperties($type, $key = 'default')
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
     * @param string $key Unique group key
     *
     * @return array
     */
    public function getMetaTagProperties($key = 'default')
    {
        return $this->getTagProperties('meta', $key);
    }

    /**
     * Get link tag properties.
     *
     * @param string $key Unique group key
     *
     * @return array
     */
    public function getLinkTagProperties($key = 'default')
    {
        return $this->getTagProperties('link', $key);
    }

    /**
     * Render tags properties.
     *
     * @param string $type     Type of tag
     * @param string $template Tag template
     * @param string $key      Unique group key
     *
     * @return string
     */
    protected function renderTagProperties($type, $template, $key = 'default')
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
     * @param string $key Unique group key
     *
     * @return string
     */
    public function renderMetaTagProperties($key = 'default')
    {
        return $this->renderTagProperties('meta', '<meta %s />', $key);
    }

    /**
     * Render link tag properties.
     *
     * @param string $key Unique group key
     *
     * @return string
     */
    public function renderLinkTagProperties($key = 'default')
    {
        return $this->renderTagProperties('link', '<link %s />', $key);
    }
}
