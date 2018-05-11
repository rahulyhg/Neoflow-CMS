<?php

namespace Neoflow\Module\Search;

class Result
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * Constrcutor.
     *
     * @param string $url         URL to the search result
     * @param string $title       Title of the search result
     * @param string $description Description of the search result
     * @param int    $quality     Search result quality
     */
    public function __construct(string $url, string $title, string $description = '', int $quality = 50)
    {
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Get URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get focused description.
     *
     * @param string $query Search query
     * @param int    $range Focus range (number of chars left and right of found search query in description)
     *
     * @return string
     */
    public function getFocusedDescription(string $query, int $range = 50, string $prefix = '...', string $postfix = '...'): string
    {
        $description = strip_tags($this->description);

        $start = max(mb_stripos($description, $query) - $range, 0);
        $length = mb_strlen($query) + ($range * 2);

        if (0 === $start) {
            $prefix = '';
        }

        if ($length >= mb_strlen($description)) {
            $postfix = '';
        }

        return $prefix.trim(mb_substr($description, $start, $length)).$postfix;
    }
}
