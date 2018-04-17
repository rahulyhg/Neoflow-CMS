<?php

namespace Neoflow\Framework\Handler;

use DateTime;
use Neoflow\Framework\AppTrait;
use RuntimeException;

class Translator {

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var array
     */
    protected $translation = [];

    /**
     * @var array
     */
    protected $fallbackTranslation = [];

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * @var string
     */
    protected $dateTimeFormat = 'Y-m-d h:m';

    /**
     * @var string
     */
    protected $fallbackDateFormat = 'Y-m-d';

    /**
     * @var string
     */
    protected $fallbackDateTimeFormat = 'Y-m-d h:m';

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var string
     */
    protected $fallbackLanguageCode = 'en';

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
        $this->setTimezone($this->config()->get('app')->get('timezone'));

        $this->loadTranslations();

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
     * Load translations
     * @return self
     */
    protected function loadTranslations(): self
    {
        // Load translation file
        $translationFile = $this->config()->getApplicationPath('/i18n/' . $this->languageCode . '.php');
        $this->loadTranslationFile($translationFile);

        // Load fallback translation file
        $fallbackTranslationFile = $this->config()->getApplicationPath('/i18n/' . $this->fallbackLanguageCode . '.php');
        $this->loadTranslationFile($fallbackTranslationFile, true);

        return $this;
    }

    /**
     * Get fallback language code.
     *
     * @return string
     */
    public function getFallbackLanguageCode(): string
    {
        return $this->fallbackLanguageCode;
    }

    /**
     * Get default language code (first language code in config).
     *
     * @return string
     */
    public function getDefaultLanguageCode(): string
    {
        $languages = $this->config()->get('app')->get('languages');

        return isset($languages[0]) ? $languages[0] : $this->fallbackLanguageCode;
    }

    /**
     * Detect language code from URI, HTTP header or session.
     *
     * @return string
     */
    protected function detectLanguageCode(): string
    {
        // Get language code from HTTP header
        $httpLanguageCode = strtolower($this->app()->get('request')->getHttpLanguage());

        // Get language code from uri
        $uriLanguageCode = strtolower($this->app()->get('request')->getUrlLanguage());

        // Get language code from session
        $sessionLanguageCode = $this->session()->get('_LANGUAGE_CODE');

        // Get selectable language codes
        $languageCodes = $this->getLanguageCodes();

        // Set current language code
        if ($uriLanguageCode && in_array($uriLanguageCode, $languageCodes)) {
            return $this->languageCode = $uriLanguageCode;
        } elseif ($sessionLanguageCode && in_array($sessionLanguageCode, $languageCodes)) {
            return $this->languageCode = $sessionLanguageCode;
        } elseif ($httpLanguageCode && in_array($httpLanguageCode, $languageCodes)) {
            return $this->languageCode = $httpLanguageCode;
        }

        return $this->getDefaultLanguageCode();
    }

    /**
     * Get selectable language codes.
     *
     * @return array
     */
    public function getLanguageCodes(): array
    {
        return $this->config()->get('app')->get('languages');
    }

    /**
     * Get active language code.
     *
     * @return string
     */
    public function getActiveLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * Set date format.
     *
     * @param string $format     Date format
     * @param bool   $isFallback Set TRUE if date format is the fallback format
     *
     * @return self
     */
    public function setDateFormat(string $format, bool $isFallback = false): self
    {
        if ($isFallback) {
            $this->fallbackDateFormat = $format;
        } else {
            $this->dateFormat = $format;
        }

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
    public function setDateTimeFormat(string $format, bool $isFallback = false): self
    {
        if ($isFallback) {
            $this->fallbackDateTimeFormat = $format;
        } else {
            $this->dateTimeFormat = $format;
        }

        return $this;
    }

    /**
     * Set timezone.
     *
     * @param string $timezone Timezone
     *
     * return self
     */
    public function setTimezone(string $timezone): self
    {
        date_default_timezone_set($timezone);

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
    public function addTranslation(array $translation, bool $isFallback = false): self
    {
        if ($isFallback) {
            $this->fallbackTranslation = array_merge($this->fallbackTranslation, $translation);
        } else {
            $this->translation = array_merge($this->translation, $translation);
        }

        return $this;
    }

    /**
     * Load translation file.
     *
     * @param string $translationFilePath Translation file path
     * @param bool   $isFallback          Set TRUE if the file contains fallback translations
     * @param bool   $silent              Set TRUE to disable runtime exception when translation file won't exists
     *
     * @return self
     *
     * @throws RuntimeException
     */
    protected function loadTranslationFile(string $translationFilePath, bool $isFallback = false, bool $silent = false): self
    {
        if (is_file($translationFilePath)) {
            $translationData = include $translationFilePath;
            if (isset($translationData['translation'])) {
                $this->addTranslation($translationData['translation'], $isFallback);
            }
            if (isset($translationData['dateFormat'])) {
                $this->setDateFormat($translationData['dateFormat'], $isFallback);
            }
            if (isset($translationData['dateTimeFormat'])) {
                $this->setDateTimeFormat($translationData['dateTimeFormat'], $isFallback);
            }
            $this->logger()->debug('Translation file loaded', [
                'File' => $translationFilePath,
            ]);
        } elseif (!$silent) {
            throw new RuntimeException('Translation file "' . $translationFilePath . '" not found');
        }

        return $this;
    }

    /**
     * Translate key and values.
     *
     * @param string $key             Translation key
     * @param array  $values          Values for translation
     * @param bool   $plural          State if translation should be plural
     * @param bool   $errorPrefix     State if prefix should be added when an error appears
     * @param bool   $translateValues State whether values should be translated too
     *
     * @return string
     */
    public function translate(string $key, array $values = [], bool $plural = false, bool $errorPrefix = true, bool $translateValues = true): string
    {
        $translatorConfig = $this->config()->get('translator');

        if (isset($this->translation[$key])) {
            $translation = $this->translation[$key];
        } elseif (isset($this->fallbackTranslation[$key])) {
            $translation = $this->fallbackTranslation[$key];
            if ($errorPrefix) {
                $translation = $translatorConfig->get('fallbackPrefix') . $translation;
                $this->logger()->warning('Translated "' . $key . '" with fallback translation to "' . $translation . '"');
            }
        } else {
            $translation = $key;
            if ($errorPrefix) {
                $translation = $translatorConfig->get('notFoundPrefix') . $translation;
                $this->logger()->warning('Translation "' . $key . '" not found');
            }
        }

        $translation = explode('|', $translation);
        if ($plural && isset($translation[1])) {
            $translation = $translation[1];
        } else {
            $translation = $translation[0];
        }

        foreach ($values as $placeholder => $value) {
            if (is_array($value)) {
                $value = implode(' / ', $value);
            }
            if (is_a($value, '\\DateTime')) {
                $value = $this->formatDate($value);
            }
            if ($translateValues) {
                $value = $this->translate($value, [], '', false);
            }
            $value = $this->translate($value, [], '', false);
            $translation = str_replace('{' . $placeholder . '}', $value, $translation);
        }

        return $translation;
    }

    /**
     * Get date format.
     *
     * @param string $timeFormat
     *
     * @return string
     */
    public function getDateFormat(string $timeFormat = ''): string
    {
        return $this->dateFormat . $timeFormat;
    }

    /**
     * Format DateTime object.
     *
     * @param DateTime $dateTime
     * @param bool     $formatWithTime
     *
     * @return string
     */
    public function formatDateTime(DateTime $dateTime, bool $formatWithTime = true): string
    {
        if ($formatWithTime) {
            return $dateTime->format($this->dateTimeFormat);
        }

        return $dateTime->format($this->dateFormat);
    }

    /**
     * Format timestamp.
     *
     * @param int  $timestamp
     * @param bool $formatWithTime
     *
     * @return string
     */
    public function formatTimestamp(int $timestamp, bool $formatWithTime = true): string
    {
        if ($formatWithTime) {
            return date($this->dateTimeFormat, $timestamp);
        }

        return date($this->dateFormat, $timestamp);
    }

}
