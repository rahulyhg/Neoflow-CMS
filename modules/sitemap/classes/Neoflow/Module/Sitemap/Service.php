<?php

namespace Neoflow\Module\Sitemap;

use DOMDocument;
use LengthException;
use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Model\PageModel;
use Neoflow\Module\Sitemap\Model\SettingModel;
use Neoflow\Module\Sitemap\Model\UrlModel;
use RuntimeException;
use SimpleXMLElement;

class Service extends AbstractService
{
    /**
     * @var Model\SettingModel
     */
    protected $settings;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->settings = Model\SettingModel::findById(1);
        $this->settings->setReadOnly();
    }

    /**
     * Register url.
     *
     * @param string $location        URL (of page)
     * @param int    $lastModified    Last modified timestamp (of page)
     * @param string $changeFrequency Change frequency of URL (page content)
     * @param float  $priority        Priority of URL
     *
     * @return self
     *
     * @see https://www.sitemaps.org/de/protocol.html
     */
    public function register(string $location, int $lastModified = null, string $changeFrequency = null, float $priority = null): self
    {
        $url = Model\UrlModel::create([
                'loc' => $location,
                'lastmod' => date('Y-m-d\TH:i:sP', $lastModified ?: time()),
                'changefreq' => $changeFrequency ?: $this->settings->default_changefreq,
                'priority' => $priority ?: $this->settings->default_priotity,
        ]);

        $url->validate();
        $url->save();

        return $this;
    }

    /**
     * Unregister URL by location.
     *
     * @param string $location URL (of page)
     *
     * @return bool
     */
    public function unregister(string $location): bool
    {
        return UrlModel::deleteByLoc($location);
    }

    /**
     * Get sitemap path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->config()->getPath('sitemap.xml');
    }

    /**
     * Get sitemap file.
     *
     * @return File|null
     *
     * @throws \Neoflow\Filesystem\Exception\FileException
     */
    public function getFile()
    {
        $sitemapPath = $this->config()->getPath('sitemap.xml');
        if (is_file($sitemapPath)) {
            return new File($sitemapPath);
        }

        return null;
    }

    /**
     * Generate sitemap XML.
     *
     * @return string
     *
     * @throws RuntimeException
     * @throws LengthException
     */
    public function generate(): string
    {
        $urls = UrlModel::findAll();

        // Get pages of each active language
        $languages = $this->settings()->getLanguages();
        foreach ($languages as $language) {
            $pages = PageModel::repo()
                ->where('is_active', '=', true)
                ->where('language_id', '=', $language->id())
                ->caching(false)
                ->fetchAll();

            // Add pages to urls
            foreach ($pages as $page) {
                $urls->addFirst(Model\UrlModel::create([
                        'loc' => $page->getUrl(),
                        'lastmod' => date('Y-m-d\TH:i:sP'),
                        'changefreq' => $this->settings->default_changefreq,
                        'priority' => $this->settings->default_priotity,
                ]));
            }
        }

        if ($urls->count() > 50000) {
            throw new RuntimeException('Sitemap has more than 50\'000 urls');
        }

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        foreach ($urls as $url) {
            $row = $xml->addChild('url');

            $row->addChild('loc', htmlspecialchars($url->loc, ENT_QUOTES, 'UTF-8'));

            if ($url->lastmod) {
                $row->addChild('lastmod', $url->lastmod);
            }

            if ($url->changefreq) {
                $row->addChild('changefreq', $url->changefreq);
            }

            if ($url->priority) {
                $row->addChild('priority', $url->priority);
            }
        }

        $xmlOutput = $xml->asXML();

        if (mb_strlen($xmlOutput) > 10485760) {
            throw new LengthException('Sitemap size is larger than 10MB');
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlOutput);

        return $dom->saveXML();
    }

    /**
     * Generate sitemap as file.
     *
     * @return bool
     */
    public function generateAsFile(): bool
    {
        $sitemapXml = $this->generate();

        $sitemapPath = $this->getPath();

        return (bool) file_put_contents($sitemapPath, $sitemapXml);
    }

    /**
     * Get sitemap settings.
     *
     * @return SettingModel
     */
    public function getSettings(): SettingModel
    {
        return $this->settings;
    }
}
