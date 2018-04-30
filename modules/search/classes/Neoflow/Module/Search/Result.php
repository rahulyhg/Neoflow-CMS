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
     * Constrcutor
     * @param string $url URL to the search result
     * @param string $title Title of the search result
     * @param string $description Description of the search result
     * @param int $quality Search result quality
     */
    public function __construct(string $url, string $title, string $description = '', int $quality = 50)
    {
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Get URL
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
