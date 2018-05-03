<?php
namespace Neoflow\CMS\Handler;

use Neoflow\CMS\AppTrait;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\Framework\Handler\Translator as FrameworkTranslator;

class Translator extends FrameworkTranslator
{

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Load translations.
     *
     * @return self
     */
    protected function loadTranslations(): FrameworkTranslator
    {
        // Check whether translation is already cached
        $cacheKey = 'translations-' . $this->languageCode;
        if ($this->cache()->exists($cacheKey)) {
            // Fetch translations from cache
            $translations = $this->cache()->fetch($cacheKey);

            foreach ($translations as $key => $value) {
                $this->{$key} = $value;
            }
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
                'fallbackDateTimeFormat' => $this->fallbackDateTimeFormat,], 0, ['cms_core', 'cms_translator', 'cms_translations']);
        }

        return $this;
    }

    /**
     * Get current language.
     *
     * @return LanguageModel
     */
    public function getCurrentLanguage(): LanguageModel
    {
        $activeLanguage = $this->settings()->getLanguages()->where('code', $this->languageCode)->first();
        if ($activeLanguage) {
            return $activeLanguage;
        }

        return $this->settings()->getDefaultLanguage();
    }

    /**
     * Get current language code.
     *
     * @return string
     */
    public function getCurrentLanguageCode(): string
    {
        // Get current language code from database if connection is etablished
        if ($this->app()->get('database')) {
            $activeLanguage = $this->settings()->getLanguages()->where('code', $this->languageCode)->first();
            if ($activeLanguage) {
                return $activeLanguage->code;
            }
        }
        $languages = $this->config()->get('app')->get('languages');
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

        return $this->config()->get('app')->get('languages')[0];
    }
}
