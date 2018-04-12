<?php
namespace Neoflow\Framework;

use ErrorException;
use Neoflow\Framework\Common\Container;
use Neoflow\Framework\Core\AbstractService;
use Neoflow\Framework\Handler\Config;
use Neoflow\Framework\Handler\Engine;
use Neoflow\Framework\Handler\Loader;
use Neoflow\Framework\Handler\Logging\Logger;
use Neoflow\Framework\Handler\Router;
use Neoflow\Framework\Handler\Translator;
use Neoflow\Framework\HTTP\Request;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\HTTP\Session;
use Neoflow\Framework\Persistence\Caching\AbstractCache;
use Neoflow\Framework\Persistence\Caching\ApcCache;
use Neoflow\Framework\Persistence\Caching\ApcuCache;
use Neoflow\Framework\Persistence\Caching\DummyCache;
use Neoflow\Framework\Persistence\Caching\FileCache;
use Neoflow\Framework\Persistence\Database;
use OutOfRangeException;
use Throwable;
use function get_exception_trace;

class App extends Container
{

    /**
     * @var App
     */
    protected static $instance;

    /**
     * Publish application.
     *
     * @param float    $startTime      Application start time in milliseconds
     * @param Loader $loader         Loader instance
     * @param string $configFilePath Config file path
     */
    public function initialize(float $startTime, Loader $loader, string $configFilePath): self
    {
        // Safe current app instance
        self::$instance = $this;

        // Initialize counter
        $this->set('startTime', $startTime);
        $this->set('executedQueries', 0);
        $this->set('cachedQueries', 0);
        $this->set('databaseConnections', 0);

        // Set loader
        $this->set('loader', $loader);

        // Create and set config
        $config = Config::createByFile($configFilePath);
        $this->setConfig($config);

        // Create logger
        $this->set('logger', new Logger());

        // Register error handler
        $this->registerErrorHandler();

        // Set and create cache
        $this->setCache();

        // Etablish connection and set database
        $this->setDatabase();

        // Create and set session
        $this->setSession();

        // Create and set request
        $this->set('request', new Request());

        // Create and set engine
        $this->set('engine', new Engine());

        // Create and set router
        $this->set('router', new Router());

        // Create and set translator
        $this->set('translator', new Translator());

        // Create and register services
        $this->registerServices();

        $this->get('logger')->info('Application created');

        return $this;
    }

    /**
     * Get execution time in seconds.
     *
     * @return float
     */
    public function getExecutionTime()
    {
        return microtime(true) - $this->get('startTime');
    }

    /**
     * Get app instance.
     *
     * @return App
     */
    public static function instance(): self
    {
        return self::$instance;
    }

    /**
     * Publish application, send response and end script.
     */
    public function publish(): void
    {
        if (!$this->exists('response')) {
            $this->set('response', new Response());
        }

        if (!$this->get('response')->isSent()) {
            $this->get('response')->send();
        }

        $this->get('isPublished', true);
        $this->get('logger')->info('Application published');
        exit;
    }

    /**
     * Execute application and create response.
     *
     * @return self
     */
    public function execute(): self
    {
        $response = $this->get('router')->execute();
        $this->set('response', $response);

        $this->get('logger')->info('Application executed');

        return $this;
    }

    /**
     * Register service.
     *
     * @param AbstractService $service Service instance
     * @param string          $name    Name of service
     *
     * @return self
     */
    public function registerService(AbstractService $service, string $name = ''): self
    {
        if (defined($service->getReflection()->getName() . '::NAME')) {
            $name = $service->getReflection()->getName()::NAME;
        } elseif (0 === mb_strlen($name)) {
            $name = str_replace('service', '', strtolower($service->getReflection()->getShortName()));
        }

        $this->get('services')->set($name, $service);

        return $this;
    }

    /**
     * Get service.
     *
     * @param string $name Name of service
     *
     * @return AbstractService
     *
     * @throws OutOfRangeException
     */
    public function getService($name): AbstractService
    {
        if ($this->hasService($name)) {
            return $this->get('services')->get($name);
        }
        throw new OutOfRangeException('Service "' . $name . '" not found');
    }

    /**
     * Check whether the service is registerd.
     *
     * @param string $name Name of service
     *
     * @return bool
     */
    public function hasService($name): bool
    {
        return $this->get('services')->exists($name);
    }

    /**
     * Register error handler.
     */
    public function registerErrorHandler(): void
    {
        set_error_handler([$this, 'errorHandler'], E_ALL);
        register_shutdown_function([$this, 'shutdownFunction']);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * Create and register services.
     *
     * @return self
     */
    protected function registerServices(): self
    {
        $this->set('services', []);

        // Get service class names
        $serviceClassNames = $this->get('config')->get('services');

        if (count($serviceClassNames)) {
            foreach ($serviceClassNames as $name => $serviceClassName) {
                if (!is_string($name)) {
                    $name = '';
                }

                // Create service
                $service = new $serviceClassName();

                // Register service
                $this->registerService($service, $name);
            }

            $this->get('logger')->info('Services created');
        } else {
            $this->get('logger')->info('No services created');
        }

        return $this;
    }

    /**
     * Error handler.
     *
     * @param int    $code    Error code
     * @param string $message Error message
     * @param string $file    File where the error comes from
     * @param string $line    Line of file where the error comes from
     *
     * @throws ErrorException
     */
    public function errorHandler(int $code, string $message, string $file, int $line): void
    {
        $ex = new ErrorException($message, $code, E_ERROR, $file, $line);
        $this->exceptionHandler($ex);
        exit;
    }

    /**
     * Exception handler.
     *
     * @param Throwable $ex Throwable instance (mostly exceptions)
     */
    public function exceptionHandler(Throwable $ex): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $this->get('logger')->logException($ex);

        $content = str_replace(['[title]', '[message]', '[exception]', '[time]', '[trace]'], ['Fatal server error', $ex->getMessage(), get_class($ex), date('c'), get_exception_trace($ex, true, true)], '<!DOCTYPE html>
                        <html>
                            <head>
                                <meta charset="UTF-8" />
                                <title>[title]</title>
                            </head>
                            <body>
                                <h1>[title]</h1>
                                <h2>[exception]: [message]</h2>
                                <hr />
                                <p><small>[trace]</small></p>
                                <hr />
                                <p>[time]</p>
                            </body>
                        </html>');
        die($content);
    }

    /**
     * Shutdown function.
     *
     * @throws ErrorException
     */
    public function shutdownFunction(): void
    {
        $error = error_get_last();
        if (E_ERROR === $error['type']) {
            $this->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * Get remote address.
     *
     * @return string
     */
    public function getRemoteAddress(): string
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown_remote_address';
    }

    /**
     * Create and set cache.
     *
     * @param AbstractCache $cache Precreated cache
     *
     * @return self
     */
    protected function setCache(AbstractCache $cache = null): self
    {
        if (!$cache) {

            // Get cache type
            $cacheConfig = $this->get('config')->get('cache');

            $cache = new DummyCache();

            $cacheType = $cacheConfig->get('type');
            if ($cacheType) {
                if ($cacheType === 'acpu' || ('auto' === $cacheType && extension_loaded('apcu') && ini_get('apc.enabled'))) {
                    $cache = new ApcuCache($this);
                } elseif ($cacheType === 'apc' || ('auto' === $cacheType && extension_loaded('apc') && ini_get('apc.enabled'))) {
                    $cache = new ApcCache($this);
                } elseif ($cacheType === 'file' || 'auto' === $cacheType) {
                    $cache = new FileCache($this);
                }
            }
        }

        return $this->set('cache', $cache);
    }

    /**
     * Create and set session.
     *
     * @param Session $session Precreated and unstarted session
     *
     * @return self
     */
    protected function setSession(Session $session = null): self
    {
        if (!$session) {
            // Get session config
            $name = $this->get('config')->get('session')->get('name');
            $lifetime = $this->get('config')->get('session')->get('lifetime');

            // Create session
            $session = new Session($name, $lifetime);
        }

        // Start session
        $session->start();

        return $this->set('session', $session);
    }

    /**
     * Etablish connection and set database.
     *
     * @param Database $database Precreated and etablished database connection
     *
     * @return self
     */
    protected function setDatabase(Database $database = null): self
    {
        if (!$database) {
            // Get database config
            $config = self::instance()->get('config')->get('database');

            // Etablish and create database connection
            $database = Database::connect($config->get('host'), $config->get('dbname'), $config->get('username'), $config->get('password'), $config->get('charset'), [
                    Database::ATTR_PERSISTENT => false,
                    Database::ATTR_ERRMODE => Database::ERRMODE_EXCEPTION,
                    Database::ATTR_STRINGIFY_FETCHES => false,
            ]);
        }

        return $this->set('database', $database);
    }
}
