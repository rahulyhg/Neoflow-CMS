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
            parent::loadTranslations();
        }

        return $this;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->cache()->store('translations-' . $this->languageCode, [
            'translation' => $this->translation,
            'fallbackTranslation' => $this->fallbackTranslation,
            'dateFormat' => $this->dateFormat,
            'dateTimeFormat' => $this->dateTimeFormat,
            'fallbackDateFormat' => $this->fallbackDateFormat,
            'fallbackDateTimeFormat' => $this->fallbackDateTimeFormat,], 0, ['cms_core', 'cms_translator', 'cms_translations']);
    }

    /**
     * Load translation file.
     *
     * @param string $translationFilePath Translation file path
     * @param bool   $isFallback          Set TRUE if the file contains fallback translations
     * @param bool   $silent              Set TRUE to disable runtime exception when translation file won't exists
     *
     * @return self
     */
    public function loadTranslationFile(string $translationFilePath, bool $isFallback = false, bool $silent = false): FrameworkTranslator
    {
        return parent::loadTranslationFile($translationFilePath, $isFallback, $silent);
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
