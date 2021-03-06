<?php

namespace Neoflow\CMS;

use Neoflow\CMS\Handler\Config;
use Neoflow\CMS\Handler\Router;
use Neoflow\CMS\Handler\Translator;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\SettingModel;
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

        // Create service registry
        $this->set('services', []);

        // Set and create cache
        $this->setCache();

        // Establish connection and set database
        $this->setDatabase();

        // Create and set request
        $this->set('request', new Request());

        // Fetch and set CMS settings
        $this->setSettings();

        // Create and set session
        $this->setSession();

        // Create and set engine
        $this->set('engine', new Engine());

        // Set CMS-specific meta properties
        $this->get('engine')->addMetaTagProperties([
            'name' => 'description',
            'content' => $this->get('settings')->website_description,
        ], 'description')->addMetaTagProperties([
            'name' => 'keywords',
            'content' => $this->get('settings')->website_keywords,
        ], 'keywords')->addMetaTagProperties([
            'name' => 'author',
            'content' => $this->get('settings')->website_author,
        ], 'author');

        // Create and set translator
        $this->set('translator', new Translator());

        // Create and set router
        $this->set('router', new Router());

        // Install modules and themes updates
        $this->installExtensionUpdates();

        // Fetch and set modules
        $this->setModules();

        // Set themes from settings
        $this->setThemes();

        // Initialize CMS modules
        $this->get('modules')->each(function ($module) {
            $module->getManager()->initialize();
        });
        $this->get('logger')->info('CMS modules initialized');

        // Add frontend index route
        $this->get('router')->addRoutes([
            'frontend_index', 'any', '/(url:uri)', 'Neoflow\\CMS\\Controller\\Frontend@index',
        ]);

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

            if ($ex instanceof HttpException) {
                $context = ['url' => function_exists('request_url') ? request_url() : 'Unknown'];
                $this->get('logger')->warning($ex->getMessage(), $context);
            } else {
                $this->get('logger')->logException($ex);
            }

            $this->execute($response)->publish();

            exit;
        } catch (Throwable $e) {
            parent::exceptionHandler($ex);
        }
    }

    /**
     * Execute application and create response.
     *
     * @param Response|null $response Pre-created response
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
     * Establish connection and set database.
     *
     * @param Database $database Precreated and established database connection
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
        // Fetch only when database connection is established
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
     * Install modules and themes updates (but only when updateFolderPath as flash exists).
     *
     * @return self
     */
    protected function installExtensionUpdates(): self
    {
        if ($this->get('database')) {
            $updateFolderPath = $this->get('session')->getFlash('updateFolderPath');
            if (!empty($updateFolderPath)) {
                $this->service('update')->installExtensionUpdates($updateFolderPath);
            }
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
        // Fetch only when database connection is established
        if ($this->get('database')) {
            // Fetch CMS modules
            $modules = ModuleModel::findAllByColumn('is_active', true);
            $modules->each(function ($module) {
                // Load functions and add class directory
                $this->get('loader')->loadFunctionsFromDirectory($module->getPath('functions'))->addClassDirectory($module->getPath('classes'));

                // Get translator
                $translator = $this->get('translator');

                if (!$translator->isCached()) {
                    // Load translation file
                    $translationFilePath = $module->getPath('/i18n/'.$translator->getCurrentLanguageCode().'.php');
                    $translator->loadTranslationFile($translationFilePath, false, true);

                    // Load fallback translation file
                    $fallbackTranslationFilePath = $module->getPath('/i18n/'.$translator->getFallbackLanguageCode().'.php');
                    $translator->loadTranslationFile($fallbackTranslationFilePath, true, true);
                }

                // Get router
                $router = $this->get('router');

                if (!$router->isCached()) {
                    // Load route file
                    $routeFilePath = $module->getPath('/routes.php');
                    $router->loadRouteFile($routeFilePath, true);
                }
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

        // Fetch and add only when database connection is established
        if ($this->get('database')) {
            $themes->add($this->get('settings')->getFrontendTheme());
            $themes->add($this->get('settings')->getBackendTheme());
        }

        $themes->each(function ($theme) {
            // Load functions and add class directory
            $this->get('loader')->loadFunctionsFromDirectory($theme->getPath('functions'))->addClassDirectory($theme->getPath('classes'));

            // Get translator
            $translator = $this->get('translator');

            // Load translation file
            $translationFile = $theme->getPath('/i18n/'.$translator->getCurrentLanguageCode().'.php');
            $translator->loadTranslationFile($translationFile, false, true);

            // Load fallback translation file
            $fallbackTranslationFile = $theme->getPath('/i18n/'.$translator->getFallbackLanguageCode().'.php');
            $translator->loadTranslationFile($fallbackTranslationFile, true, true);
        });

        $this->get('logger')->info('CMS themes set from settings');

        return $this->set('themes', $themes);
    }
}
