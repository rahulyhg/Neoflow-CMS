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
     * @var bool
     */
    protected $cached = false;

    /**
     * Load translations.
     *
     * @return self
     */
    protected function loadTranslations(): FrameworkTranslator
    {
        // Check whether translations are already cached
        if ($this->cache()->exists('translations-' . $this->languageCode)) {

            $this->cached = true;

            // Fetch and set translation data from cache
            $translations = $this->cache()->fetch('translations-' . $this->languageCode);
            $this->translation = $translations['translation'];
            $this->fallbackTranslation = $translations['fallbackTranslation'];

            if ($this->cache()->exists('dateformats-' . $this->languageCode)) {
                $dateFormats = $this->cache()->fetch('dateformat-' . $this->languageCode);
                $this->dateFormat = $dateFormats['dateFormat'];
                $this->fallbackDateFormat = $dateFormats['fallbackDateFormat'];
            }

            if ($this->cache()->exists('datetimeformats-' . $this->languageCode)) {
                $dateTimeFormats = $this->cache()->fetch('datetimeformats-' . $this->languageCode);
                $this->dateTimeFormat = $dateTimeFormats['dateTimeFormat'];
                $this->facllbackDateTimeFormat = $dateTimeFormats['fallbackDateTimeFormat'];
            }
        } else {
            parent::loadTranslations();
        }

        return $this;
    }

    /**
     * Add translation.
     *
     * @param array $translation Translation list
     * @param bool  $isFallback  Set TRUE if translations are the fallback translations
     *
     * @return self
     */
    public function addTranslation(array $translation, bool $isFallback = false): FrameworkTranslator
    {
        parent::addTranslation($translation, $isFallback);

        $this->cache()->store('translations-' . $this->languageCode, [
            'translation' => $this->translation,
            'fallbackTranslation' => $this->fallbackTranslation
            ], 0, ['cms_core', 'cms_translator', 'cms_translations']);

        return $this;
    }

    /**
     * Set date format.
     *
     * @param string $format     Date format
     * @param bool   $isFallback Set TRUE if date format is the fallback format
     *
     * @return self
     */
    public function setDateFormat(string $format, bool $isFallback = false): FrameworkTranslator
    {
        parent::setDateFormat($format, $isFallback);

        $this->cache()->store('dateformats-' . $this->languageCode, [
            'dateFormat' => $this->dateFormat,
            'fallbackDateFormat' => $this->fallbackDateFormat], 0, ['cms_core', 'cms_translator', 'cms_translations']);

        return $this;
    }

    /**
     * Set date time format.
     *
     * @param string $format     Date time format
     * @param bool   $isFallback Set TRUE if date time format is the fallback format
     *
     * @return self
     */
    public function setDateTimeFormat(string $format, bool $isFallback = false): FrameworkTranslator
    {
        parent::setDateTimeFormat($format, $isFallback);

        $this->cache()->store('datetimeformats-' . $this->languageCode, [
            'dateTimeFormat' => $this->dateTimeFormat,
            'fallbackDateTimeFormat' => $this->fallbackDateTimeFormat], 0, ['cms_core', 'cms_translator', 'cms_translations']);

        return $this;
    }

    /**
     * Check whether translation data is cached or not
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->cached;
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
