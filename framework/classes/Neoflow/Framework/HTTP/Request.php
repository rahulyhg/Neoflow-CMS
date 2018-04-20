<?php
namespace Neoflow\Framework\HTTP;

use Neoflow\Framework\AppTrait;
use Neoflow\Framework\Common\Container;
use OutOfRangeException;

class Request
{

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var bool
     */
    protected $hasFiles = false;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->data = new Container();

        $this->data->set('cookies', new Container($_COOKIE, true));
        $this->data->set('get', new Container($_GET, true, true));

        if ($this->isHttpMethod('post')) {
            $this->data->set('post', new Container($_POST, true, true));

            if (isset($_FILES)) {
                $this->hasFiles = true;

                // ReArray files data
                $files = [];
                foreach ($_FILES as $key => $file) {
                    if (is_array($file['name'])) {
                        $files[$key] = normalize_post_files($file);
                    } else {
                        $files[$key] = $file;
                    }
                }

                $this->data->set('files', new Container($files, true));
            } else {
                $this->data->set('files', new Container([], true));
            }
        } else {
            $this->data->set('post', new Container([], true, true));
        }

        $this->logger()->info('Request interpreted', [
            'HTTP Method' => $this->getHttpMethod(),
            'HTTP Language' => $this->getHttpLanguage(),
            'URL Path' => $this->getUrlPath(true),
        ]);
    }

    /**
     * Get URL.
     *
     * @params bool $queryParams Set TRUE to URL with query parameters
     *
     * @return string
     */
    public function getUrl(bool $queryParams = false): string
    {
        return request_url(true, $queryParams);
    }

    /**
     * Check HTTP method of request.
     *
     * @param string $method HTTP method to match with e.g. GET, POST, PUT, HEAD
     *
     * @return bool
     */
    public function isHttpMethod(string $method): bool
    {
        return $this->getHttpMethod() === strtoupper($method);
    }

    /**
     * Check URL path of request.
     *
     * @param string $urlPath      URL path to match with
     * @param bool   $languageCode Set TRUE to get language in the path
     * @param bool   $basePath     Set TRUE to get the base path
     *
     * @return bool
     */
    public function isUrlPath(string $urlPath, bool $languageCode = false, bool $basePath = false): bool
    {
        return $this->getUrlPath($languageCode, $basePath) === $urlPath;
    }

    /**
     * Get HTTP method of request.
     *
     * @return string
     */
    public function getHttpMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get language code from HTTP header.
     *
     * @return string
     */
    public function getHttpLanguage(): string
    {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    /**
     * Get HTTP user agent.
     *
     * @return string
     */
    public function getHttpUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Check whether request as files.
     *
     * @return bool
     */
    public function hasFiles(): bool
    {
        return $this->hasFiles;
    }

    /**
     * Get language code from URL.
     *
     * @return string|null
     */
    public function getUrlLanguage()
    {
        $urlPath = $this->getUrlPath(true);

        if (preg_match('/\/([a-z]{2})(\/|\?|$)/', substr($urlPath, 0, 4), $languageMatches)) {
            return $languageMatches[1];
        }

        return null;
    }

    /**
     * Get request data.
     *
     * @param string $key Request data key
     *
     * @return Container
     *
     * @throws OutOfRangeException
     */
    protected function getData(string $key): Container
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        throw new OutOfRangeException('Request data not found (Key: ' . $key . ')');
    }

    /**
     * Get post data.
     *
     * @return Container
     */
    public function getPostData(): Container
    {
        return $this->getData('post');
    }

    /**
     * Get post by key.
     *
     * @param string $key Post key
     *
     * @return mixed
     */
    public function getPost(string $key)
    {
        return $this->getPostData()->get($key);
    }

    /**
     * Get cookie value by key.
     *
     * @param string $key Cookie key
     *
     * @return mixed
     */
    public function getCookie(string $key)
    {
        return $this->getData('cookies')->get($key);
    }

    /**
     * Get cookies.
     *
     * @return Container
     */
    public function getCookies(): Container
    {
        return $this->getData('cookies');
    }

    /**
     * Get get data.
     *
     * @return Container
     */
    public function getGetData(): Container
    {
        return $this->getData('get');
    }

    /**
     * Get get value.
     *
     * @param string $key Get key
     *
     * @return mixed
     */
    public function getGet(string $key)
    {
        return $this->getGetData()->get($key);
    }

    /**
     * Get file data.
     *
     * @return Container
     */
    public function getFileData(): Container
    {
        return $this->getData('files');
    }

    /**
     * Get file value.
     *
     * @param string $key File key
     *
     * @return mixed
     */
    public function getFile(string $key)
    {
        return $this->getFileData()->get($key);
    }

    /**
     * Get URL path.
     *
     * @param bool $languageCode Set TRUE to get language in the path
     * @param bool $basePath     Set TRUE to get the base path
     *
     * @return string
     */
    public function getUrlPath(bool $languageCode = false, bool $basePath = false): string
    {
        // Base url
        $baseUrl = $this->config()->getUrl();
        $baseUrlPath = parse_url($baseUrl, PHP_URL_PATH);

        // Request url
        $url = $this->getUrl();
        $urlPath = parse_url($url, PHP_URL_PATH);

        // Remove base path if set TRUE
        if (!$basePath) {
            $urlPath = str_replace($baseUrlPath, '', $urlPath);
        }

        // Remove language code if set TRUE
        if (!$languageCode) {
            return preg_replace('/^(\/[a-z]{2})(\/|\?|$)/', '/', $urlPath);
        }

        return $urlPath;
    }
}
