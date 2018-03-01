<?php

namespace Neoflow\Framework;

use Neoflow\Framework\Core\AbstractService;
use Neoflow\Framework\Handler\Config;
use Neoflow\Framework\Handler\Engine;
use Neoflow\Framework\Handler\Logging\Logger;
use Neoflow\Framework\Handler\Router;
use Neoflow\Framework\Handler\Translator;
use Neoflow\Framework\HTTP\Request;
use Neoflow\Framework\HTTP\Session;
use Neoflow\Framework\Persistence\Caching\AbstractCache;
use Neoflow\Framework\Persistence\Database;
use ReflectionClass;

trait AppTrait
{
    /**
     * Get appliction.
     *
     * @return App
     */
    protected function app(): App
    {
        return App::instance();
    }

    /**
     * Get logger.
     *
     * @return Logger
     */
    public function logger(): Logger
    {
        return $this->app()->get('logger');
    }

    /**
     * Get session.
     *
     * @return Session
     */
    public function session(): Session
    {
        return $this->app()->get('session');
    }

    /**
     * Get router.
     *
     * @return Router
     */
    public function router(): Router
    {
        return $this->app()->get('router');
    }

    /**
     * Get translator.
     *
     * @return Translator
     */
    public function translator(): Translator
    {
        return $this->app()->get('translator');
    }

    /**
     * Get request.
     *
     * @return Request
     */
    public function request(): Request
    {
        return $this->app()->get('request');
    }

    /**
     * Get config.
     *
     * @return Config
     */
    public function config(): Config
    {
        return $this->app()->get('config');
    }

    /**
     * Get cache.
     *
     * @return AbstractCache
     */
    public function cache(): AbstractCache
    {
        return $this->app()->get('cache');
    }

    /**
     * Get reflection of current object.
     *
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this);
    }

    /**
     * Get engine.
     *
     * @return Engine
     */
    public function engine(): Engine
    {
        return $this->app()->get('engine');
    }

    /**
     * Get database.
     *
     * @return Database
     */
    public function database(): Database
    {
        return $this->app()->get('database');
    }

    /**
     * Get service by name.
     *
     * @param string $name
     *
     * @return AbstractService
     */
    public function getService(string $name): AbstractService
    {
        return $this->app()->getService($name);
    }
}
