<?php
namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use RuntimeException;
use Neoflow\Framework\Core\AbstractModel as FrameworkAbstractModel;

class SettingModel extends AbstractModel
{

    /**
     * @var string
     */
    public static $tableName = 'settings';

    /**
     * @var string
     */
    public static $primaryKey = 'setting_id';

    /**
     * @var array
     */
    public $language_ids = [];

    /**
     * @var array
     */
    public static $properties = ['setting_id', 'website_title', 'website_description',
        'website_keywords', 'website_author', 'theme_id', 'login_attempts', 'session_lifetime',
        'backend_theme_id', 'default_language_id', 'show_debugbar',
        'website_emailaddress', 'session_name', 'allowed_file_extensions',
        'show_error_details', 'custom_css', 'custom_js',
        'show_custom_js', 'show_custom_css', 'timezone',
    ];

    /**
     * Constructor.
     *
     * @param array $data       Data of model entity
     * @param bool  $isReadOnly State whether model entity is read-only or not
     */
    public function __construct(array $data = [], $isReadOnly = false)
    {
        parent::__construct($data, $isReadOnly);

        if ($this->app()->get('database')) {
            $this->language_ids = $this->getLanguages()->mapValue('language_id');
        }
    }

    /**
     * Overwrite config
     * @return self
     */
    public function overwriteConfig(): self
    {
        $this->config()->get('app')->setData([
            'email' => $this->website_emailaddress,
            'timezone' => $this->timezone,
            'languages' => $this->getLanguageCodes()
        ]);

        $this->config()->set('session', [
            'lifetime' => (int) $this->session_lifetime,
            'name' => $this->session_name
        ]);

        return $this;
    }

    /**
     * Get repository to fetch frontend theme.
     *
     * @return Repository
     */
    public function frontendTheme(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\ThemeModel', 'theme_id');
    }

    /**
     * Get allowed file extensions.
     *
     * @return array
     */
    public function getAllowedFileExtensions(): array
    {
        if ($this->allowed_file_extensions) {
            return explode(',', $this->allowed_file_extensions);
        }

        return [];
    }

    /**
     * Get website keywords
     *
     * @return array
     */
    public function getWebsiteKeywords(): array
    {
        if ($this->website_keywords) {
            return explode(',', $this->website_keywords);
        }

        return [];
    }

    /**
     * Get frontend theme.
     *
     * @return ThemeModel|null
     */
    public function getFrontendTheme()
    {
        $theme = $this->frontendTheme()->fetch();
        if ($theme) {
            return $theme;
        }
        return null;
    }

    /**
     * Get backend theme.
     *
     * @return ThemeModel|null
     */
    public function getBackendTheme()
    {
        $theme = $this->backendTheme()->fetch();
        if ($theme) {
            return $theme;
        }
        return null;
    }

    /**
     * Get repository to fetch backend theme.
     *
     * @return Repository
     */
    public function backendTheme(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\ThemeModel', 'backend_theme_id');
    }

    /**
     * Get repository to fetch language.
     *
     * @return Repository
     */
    public function defaultLanguage(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\LanguageModel', 'default_language_id');
    }

    /**
     * Get repository to fetch languages.
     *
     * @return Repository
     */
    public function languages(): Repository
    {
        return $this->hasManyThrough('\\Neoflow\\CMS\\Model\\LanguageModel', '\\Neoflow\\CMS\\Model\\SettingLanguageModel', 'setting_id', 'language_id');
    }

    /**
     * Validate setting.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->betweenLength(3, 50)
            ->set('website_title', 'Website title');

        $validator
            ->maxlength(150)
            ->set('website_description', 'Website description');

        $validator
            ->maxlength(250)
            ->set('website_keywords', 'Website keyword', [], true);

        $validator
            ->maxlength(50)
            ->set('website_author', 'Website author');

        $validator
            ->integer()
            ->min(3)
            ->set('login_attempts', 'Login attempt', [], true);

        $validator
            ->integer()
            ->min(300)
            ->set('session_lifetime', 'Session lifetime');

        $validator
            ->required()
            ->email()
            ->maxLength(100)
            ->set('website_emailaddress', 'E-Mailaddress');

        $validator
            ->maxlength(50)
            ->set('session_name', 'Session name');

        return (bool) $validator->validate();
    }

    /**
     * Save properties  to config
     * @return bool
     */
    public function saveToConfig(): bool
    {
        $this->config()->set('session', [
            'name' => $this->session_name,
            'lifetime' => $this->session_lifetime
        ]);

        $this->config()->get('app')->setData([
            'timezone' => $this->timezone,
            'email' => $this->website_emailaddress,
            'languages' => $this->getLanguageCodes(),
        ]);

        return $this->config()->saveAsFile();
    }

    /**
     * Save settings.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        // Set random string for session name when empty
        if (!$this->session_name) {
            $this->session_name = random_string(10);
        }

        if (parent::save($preventCacheClearing)) {
            // Save setting langauges
            if ($this->language_ids && is_array($this->language_ids)) {
                // The default language must also be selectable
                if (!in_array($this->default_language_id, $this->language_ids)) {
                    $this->language_ids[] = $this->default_language_id;
                }

                // Delete old setting languages
                SettingLanguageModel::deleteAllByColumn('setting_id', $this->id());

                // Create new setting languages
                foreach ($this->language_ids as $language_id) {
                    SettingLanguageModel::create([
                            'setting_id' => $this->id(),
                            'language_id' => $language_id,
                        ])
                        ->save($preventCacheClearing);
                }
            }

            return $this->saveToConfig();
        }

        return false;
    }

    /**
     * Get selectable languages.
     *
     * @return EntityCollection
     */
    public function getLanguages(): EntityCollection
    {
        return $this->languages()->fetchAll();
    }

    /**
     * Get selectable language codes.
     *
     * @return array
     */
    public function getLanguageCodes(): array
    {
        // Get language codes from database if connection is etablished
        if ($this->app()->get('database') && self::findById(1)) {
            return $this->getLanguages()->mapValue('code');
        }

        return $this->config()->get('app')->get('languages');
    }

    /**
     * Get default language.
     *
     * @return LanguageModel
     */
    public function getDefaultLanguage(): LanguageModel
    {
        return $this->defaultLanguage()->fetch();
    }
}
