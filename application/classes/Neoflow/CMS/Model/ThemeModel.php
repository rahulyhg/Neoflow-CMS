<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Validation\ValidationException;

class ThemeModel extends AbstractExtensionModel
{
    /**
     * @var string
     */
    public static $tableName = 'themes';

    /**
     * @var string
     */
    public static $primaryKey = 'theme_id';

    /**
     * @var array
     */
    public static $properties = [
        'theme_id', 'name', 'folder_name', 'type', 'version', 'description',
        'author', 'block_handling', 'copyright', 'license', 'identifier',
    ];

    /**
     * @var array
     */
    public static $blockHandling = [
        'grouped', 'sequential',
    ];

    /**
     * @var array
     */
    public static $type = [
        'frontend', 'backend',
    ];

    /**
     * Get theme URL.
     *
     * @param string $additionalUrlPath Additional URL path
     *
     * @return string
     */
    public function getUrl(string $additionalUrlPath = ''): string
    {
        $additionalUrlPath = str_replace('{version}', $this->version, $additionalUrlPath);

        return $this
                ->config()
                ->getThemesUrl('/'.$this->folder_name.'/'.$additionalUrlPath);
    }

    /**
     * Get theme path.
     *
     * @param string $additionalPath Additional path
     *
     * @return string
     */
    public function getPath(string $additionalPath = ''): string
    {
        return $this
                ->config()
                ->getThemesPath('/'.$this->folder_name.'/'.$additionalPath);
    }

    /**
     * Load blocks from info file.
     *
     * @return self
     *
     * @throws ValidationException
     */
    public function loadBlocks(): self
    {
        // Get info file path
        $infoFilePath = $this->getPath('info.php');

        // Fetch info data
        $info = $this->fetchInfoFile($infoFilePath);

        if (is_array($info)) {
            $blocks = BlockModel::findAll();
            $index = 0;
            foreach ($blocks as $block) {
                if (isset($info['blocks'][$index][0]) && isset($info['blocks'][$index][1])) {
                    $block->block_key = $info['blocks'][$index][0];
                    $block->title = $info['blocks'][$index][1];
                    $block->validate();
                    $block->save();
                } else {
                    $block->delete();
                }
                ++$index;
            }

            $numberOfBlocks = count($info['blocks']);
            for ($index; $index <= $numberOfBlocks; ++$index) {
                if (isset($info['blocks'][$index][0]) && isset($info['blocks'][$index][1])) {
                    BlockModel::create([
                        'block_key' => $info['blocks'][$index][0],
                        'title' => $info['blocks'][$index][1],
                    ])->save();
                }
            }
        }

        return $this;
    }

    /**
     * Load navigations from info file.
     *
     * @return self
     *
     * @throws ValidationException
     */
    public function loadNavigations(): self
    {
        // Get info file path
        $infoFilePath = $this->getPath('info.php');

        // Fetch info data
        $info = $this->fetchInfoFile($infoFilePath);

        if (is_array($info)) {
            $navigations = NavigationModel::repo()->where('navigation_id', '!=', 1)->fetchAll();
            $index = 0;
            foreach ($navigations as $navigation) {
                if (isset($info['navigations'][$index][0]) && isset($info['navigations'][$index][1])) {
                    $navigation->navigation_key = $info['navigations'][$index][0];
                    $navigation->title = $info['navigations'][$index][1];
                    $navigation->validate();
                    $navigation->save();
                } else {
                    $navigation->delete();
                }
                ++$index;
            }

            $numberOfNavigations = count($info['navigations']);
            for ($index; $index <= $numberOfNavigations; ++$index) {
                if (isset($info['navigations'][$index][0]) && isset($info['navigations'][$index][1])) {
                    NavigationModel::create([
                        'navigation_key' => $info['navigations'][$index][0],
                        'title' => $info['navigations'][$index][1],
                    ])->save();
                }
            }
        }

        return $this;
    }

    /**
     * Validate module.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function validate(): bool
    {
        parent::validate();

        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->callback(function ($name, $id) {
                return 0 === ThemeModel::repo()
                    ->where('name', '=', $name)
                    ->where('theme_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', [$this->id()])
            ->set('name', 'Name');

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->callback(function ($folder, $id) {
                return 0 === ThemeModel::repo()
                    ->where('folder_name', '=', $folder)
                    ->where('theme_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', [$this->id()])
            ->set('folder_name', 'Folder');

        $validator
            ->oneOf(static::$type)
            ->set('type', 'Type');

        if ('frontend' === $this->type) {
            $validator
                ->oneOf(static::$blockHandling)
                ->set('block_handling', 'Block Handling');
        }

        return (bool) $validator->validate();
    }

    /**
     * Delete theme.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function delete(): bool
    {
        if ($this->settings()->backend_theme_id != $this->id() && $this->settings()->theme_id != $this->id()) {
            return parent::delete();
        }
        throw new ValidationException(translate('Theme is in use and cannot be deleted.'));
    }
}
