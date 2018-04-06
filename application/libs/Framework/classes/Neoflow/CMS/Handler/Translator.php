<?php

namespace Neoflow\CMS\Handler;

use Neoflow\CMS\AppTrait;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\Framework\Handler\Translator as FrameworkTranslator;

class Translator extends FrameworkTranslator {

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set language code
        $this->languageCode = $this->detectLanguageCode();

        // Set language code to session
        $this->session()->set('_LANGUAGE_CODE', $this->languageCode);

        // Set timezone based on settings
        $this->setTimezone($this->settings()->timezone);

        $cacheKey = 'translations-' . $this->languageCode;
        if ($this->cache()->exists($cacheKey)) {
            // Fetch translations from cache
            $translations = $this->cache()->fetch($cacheKey);
            $this->translation = $translations['translation'];
            $this->fallbackTranslation = $translations['fallbackTranslation'];
            $this->dateFormat = $translations['dateFormat'];
            $this->dateTimeFormat = $translations['dateTimeFormat'];
            $this->fallbackDateFormat = $translations['fallbackDateFormat'];
            $this->fallbackDateTimeForm = $translations['fallbackDateTimeFormat'];
        } else {
            // Load translation file of each theme and module, but only when database connection is etablished
            if ($this->app()->get('database')) {

                foreach ($this->app()->get('modules') as $module) {
                    // Load translation file
                    $translationFile = $module->getPath('/i18n/' . $this->languageCode . '.php');
                    $this->loadTranslationFile($translationFile, false, true);

                    // Load fallback translation file
                    $fallbackTranslationFile = $module->getPath('/i18n/' . $this->languageCode . '.php');
                    $this->loadTranslationFile($fallbackTranslationFile, true, true);
                }

                foreach ($this->app()->get('themes') as $theme) {
                    // Load translation file
                    $translationFile = $theme->getPath('/i18n/' . $this->languageCode . '.php');
                    $this->loadTranslationFile($translationFile, false, true);

                    // Load fallback translation file
                    $fallbackTranslationFile = $theme->getPath('/i18n/' . $this->languageCode . '.php');
                    $this->loadTranslationFile($fallbackTranslationFile, true, true);
                }
            }

            // Load translation file
            $translationFile = $this->config()->getApplicationPath('/i18n/' . $this->languageCode . '.php');
            $this->loadTranslationFile($translationFile);

            // Load fallback translation file
            $fallbackTranslationFile = $this->config()->getApplicationPath('/i18n/' . $this->fallbackLanguageCode . '.php');
            $this->loadTranslationFile($fallbackTranslationFile, true);

            // Store translations to cache
            $this->cache()->store($cacheKey, [
                'translation' => $this->translation,
                'fallbackTranslation' => $this->fallbackTranslation,
                'dateFormat' => $this->dateFormat,
                'dateTimeFormat' => $this->dateTimeFormat,
                'fallbackDateFormat' => $this->fallbackDateFormat,
                'fallbackDateTimeFormat' => $this->fallbackDateTimeFormat
                    ], 0, ['system-configurations']);
        }

        $this->logger()->debug('Translator created', [
            'Language' => $this->languageCode,
            'Date format' => $this->dateFormat,
            'Date time format' => $this->dateTimeFormat,
            'Fallback language' => $this->fallbackLanguageCode,
            'Fallback date format' => $this->fallbackDateFormat,
            'Fallback date time format' => $this->fallbackDateTimeFormat,
        ]);
    }

    /**
     * Get active language code.
     *
     * @return LanguageModel
     */
    public function getActiveLanguage(): LanguageModel
    {
        $activeLanguage = $this->settings()->getLanguages()->where('code', $this->languageCode)->first();
        if ($activeLanguage) {
            return $activeLanguage;
        }

        return $this->settings()->getDefaultLanguage();
    }

    /**
     * Get active language code.
     *
     * @return string
     */
    public function getActiveLanguageCode(): string
    {
        // Get active language code from database if connection is etablished
        if ($this->app()->get('database')) {
            $activeLanguage = $this->settings()->getLanguages()->where('code', $this->languageCode)->first();
            if ($activeLanguage) {
                return $activeLanguage->code;
            }
        }
        $languages = $this->config()->get('languages');
        $index = array_search($this->languageCode, $languages);
        if (false !== $index) {
            return $languages[$index];
        }

        return $this->getDefaultLanguageCode();
    }

    /**
     * Get default language code.
     *
     * @return string
     */
    public function getDefaultLanguageCode(): string
    {
        // Get default language code from database if connection is etablished
        if ($this->app()->get('database')) {
            $defaultLanguage = $this->settings()->defaultLanguage()->fetch();
            if ($defaultLanguage) {
                return $defaultLanguage->code;
            }
        }

        return $this->config()->get('languages')[0];
    }

}
