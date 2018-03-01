<?php

namespace Neoflow\Framework\HTTP\Responsing;

use DateTime;
use InvalidArgumentException;
use Neoflow\Framework\AppTrait;

class Response
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var bool
     */
    protected $isSent = false;

    /**
     * Constructor.
     *
     * @param string $content Response content
     */
    public function __construct(string $content = '')
    {
        $this->setContent($content);
    }

    /**
     * Set cookie value.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $expire
     * @param mixed  $path
     * @param mixed  $domain
     * @param bool   $secure
     *
     * @return bool
     */
    public function setCookie(string $key, $value = '', string $expire = '+24 hour', $path = false, $domain = false, bool $secure = false): bool
    {
        // Create a date
        $date = new DateTime();
        // Modify it (+1hours; +1days; +20years; -2days etc)
        $date->modify($expire);
        // Set cookie
        return setcookie($key, $value, $date->getTimestamp(), $path, $domain, $secure, true);
    }

    /**
     * Delete cookie value.
     *
     * @param string $key
     * @param mixed  $path
     * @param mixed  $domain
     * @param bool   $secure
     *
     * @return bool
     */
    public function deleteCookie(string $key, $path = false, $domain = false, bool $secure = false)
    {
        return $this->setCookie($key, '', '-1 hour', $path, $domain, $secure);
    }

    /**
     * Send HTTP header.
     */
    protected function sendHeader(): void
    {
        if ($this->statusCode) {
            http_response_code($this->statusCode);
        }
        foreach ($this->headers as $header) {
            if (!in_array($header, headers_list())) {
                header($header);
            }
        }
    }

    /**
     * Set header.
     *
     * @param string $header
     *
     * @return self
     */
    public function setHeader(string $header): self
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * Set HTTP status code.
     *
     * @param int $statusCode
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setStatusCode(int $statusCode): self
    {
        if (StatusCode::isValid($statusCode)) {
            $this->statusCode = $statusCode;

            return $this;
        }
        throw new InvalidArgumentException('HTTP status code "'.$statusCode.'" is not valid');
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set content.
     *
     * @param string $content Response content
     *
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Send content as echo.
     */
    protected function sendContent(): void
    {
        echo $this->content;
    }

    /**
     * Send response.
     */
    public function send(): void
    {
        if (!$this->isSent) {
            $this->sendHeader();
            $this->sendContent();
            $this->isSent = true;
        }
        $this->logger()->info('Response sent');
    }

    /**
     * Check if.
     *
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->isSent;
    }
}
