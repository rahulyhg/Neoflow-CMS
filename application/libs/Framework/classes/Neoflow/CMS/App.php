<?php

namespace Neoflow\CMS;

use Neoflow\CMS\Handler\Config;
use Neoflow\CMS\Handler\Router;
use Neoflow\CMS\Handler\Translator;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Service\UpdateService;
use Neoflow\Framework\App as FrameworkApp;
use Neoflow\Framework\Handler\Engine;
use Neoflow\Framework\Handler\Loader;
use Neoflow\Framework\Handler\Logging\Logger;
use Neoflow\Framework\HTTP\Exception\HttpException;
use Neoflow\Framework\HTTP\Request;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\Persistence\Database;
use RuntimeException;
use Throwable;
use function request_url;

class App extends FrameworkApp
{
    /**
     * Publish application.
     *
     * @param float  $startTime      Application start time in milliseconds
     * @param Loader $loader         Loader instance
     * @param string $configFilePath Config file path
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function initialize(float $startTime, Loader $loader, string $configFilePath): FrameworkApp
    {
        // Safe current app instance
        self::$instance = $this;

        // Set params
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

        // Create and set request
        $this->set('request', new Request());

        // Fetch and set CMS settings
        $this->setSettings();

        // Create and set session
        $this->setSession();

        // Handle update
        $this->update();

        // Create and set engine
        $this->set('engine', new Engine());

        // Set CMS-specific meta properties
        $this->get('engine')
            ->addMetaTagProperties([
                'name' => 'description',
                'content' => $this->get('settings')->website_description,
                ], 'description')
            ->addMetaTagProperties([
                'name' => 'keywords',
                'content' => $this->get('settings')->website_keywords,
                ], 'keywords')
            ->addMetaTagProperties([
                'name' => 'author',
                'content' => $this->get('settings')->website_author,
                ], 'author');

        // Fetch and set modules
        $this->setModules();

        // Set themes from settings
        $this->setThemes();

        // Create and set router
        $this->set('router', new Router());

        // Create and set translator
        $this->set('translator', new Translator());

        // Create and register services
        $this->registerServices();

        // Initialize CMS modules
        $this->get('modules')->each(function ($module) {
            $module->getManager()->initialize();
        });
        $this->get('logger')->info('CMS modules initialized');

        // Add frontend index route
        $this->get('router')->addRoutes(['frontend_index', 'any', '/(url:uri)', '\\Neoflow\\CMS\\Controller\\Frontend@index']);

        $this->get('logger')->info('Application created');

        return $this;
    }

    /**
     * Exception handler.
     *
     * @param Throwable $ex Throwable instance (mostly exceptions)
     */
    public function exceptionHandler(Throwable $ex)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        try {
            $response = $this->get('router')->routeByKey('error_index', ['exception' => $ex]);

            $this
                ->execute($response)
                ->publish();

            if ($ex instanceof HttpException) {
                $context = [
                    'url' => function_exists('request_url') ? request_url() : 'Unknown',
                ];
                $this->get('logger')->warning($ex->getMessage(), $context);
            } else {
                $this->get('logger')->logException($ex);
            }
            exit;
        } catch (Throwable $e) {
            parent::exceptionHandler($ex);
        }
    }

    /**
     * Execute application and create response.
     *
     * @return self
     */
    public function execute(Response $response = null): FrameworkApp
    {
        if (!$response) {
            $response = $this->get('router')->execute();
        }
        $this->set('response', $response);

        // Execute CMS modules
        $this->get('modules')->each(function ($module) {
            $module->getManager()->execute();
        });

        $this->get('logger')->info('Application executed');

        return $this;
    }

    /**
     * Etablish connection and set database.
     *
     * @param Database $database Precreated and etablished database connection
     *
     * @return self
     */
    protected function setDatabase(Database $database = null): FrameworkApp
    {
        if ($database || $this->get('config')->get('database')->get('host')) {
            return parent::setDatabase($database);
        }

        return $this->set('database', false);
    }

    /**
     * Fetch and set CMS settings.
     *
     * @return self
     *
     * @throws RuntimeException
     */
    protected function setSettings(): self
    {
        // Fetch only when database connection is etablished
        if ($this->get('database')) {
            // Fetch CMS settings
            $settings = SettingModel::findById(1);
            if ($settings) {
                $settings->setReadOnly();

                // Overwrite config with CMS settings
                $settings->overwriteConfig();

                $this->get('logger')->info('CMS settings fetched');
            } else {
                throw new RuntimeException('Settings not found (ID: 1)');
            }
        } else {
            // Create CMS settings based of config
            $settings = new SettingModel();
        }

        return $this->set('settings', $settings);
    }

    /**
     * Install modules and themes update packages (but only when updateFolderPath as flash exists).
     *
     * @return self
     */
    protected function installExtensionUpdates(): self
    {
        if ($this->get('database')) {
            $updateFolderPath = $this->get('session')->getFlash('updateFolderPath');
            if (!empty($updateFolderPath)) {
                $updateService = new UpdateService();
                $updateService->installExtensionUpdates($updateFolderPath);
            }
        }

        return $this;
    }

    /**
     * Execute update listener.
     *
     * @return self
     */
    protected function update(): self
    {
        if ($this->get('database')) {
            $updateService = new UpdateService();
            $updateService->execute();
        }

        return $this;
    }

    /**
     * Fetch and set active modules.
     *
     * @return self
     */
    protected function setModules(): self
    {
        // Fetch only when database connection is etablished
        if ($this->get('database')) {
            // Fetch CMS modules
            $modules = ModuleModel::findAllByColumn('is_active', true);
            $modules->each(function ($module) {
                $this->get('loader')
                    ->loadFunctionsFromDirectory($module->getPath('functions'))
                    ->addClassDirectory($module->getPath('classes'));
            });
        } else {
            // Create empty CMS modules collection
            $modules = new EntityCollection();
        }

        $this->get('logger')->info('CMS modules fetched and set');

        return $this->set('modules', $modules);
    }

    /**
     * Set active themes from settings.
     *
     * @return self
     */
    protected function setThemes(): self
    {
        $themes = new EntityCollection();

        // Fetch and add only when database connection is etablished
        if ($this->get('database')) {
            $themes->add($this->get('settings')->getFrontendTheme());
            $themes->add($this->get('settings')->getBackendTheme());
        }

        $this->get('logger')->info('CMS themes set from settings');

        return $this->set('themes', $themes);
    }
}
