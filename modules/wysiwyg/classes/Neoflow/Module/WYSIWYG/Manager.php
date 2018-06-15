<?php

namespace Neoflow\Module\WYSIWYG;

use Neoflow\CMS\Manager\AbstractPageModuleManager;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Filesystem\Folder;

class Manager extends AbstractPageModuleManager
{
    /**
     * Add module to section.
     *
     * @param SectionModel $section Added section
     *
     * @return bool
     */
    public function add(SectionModel $section): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg/section-'.$section->id());
        if (!is_dir($mediaPath)) {
            Folder::create($mediaPath);
        }

        return (bool) Model::create([
            'section_id' => $section->id(),
            'content' => '',
        ])->save();
    }

    /**
     * Remove module from section.
     *
     * @param SectionModel $section Removed section
     *
     * @return bool
     */
    public function remove(SectionModel $section): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg/section-'.$section->id());
        if (is_dir($mediaPath)) {
            Folder::unlink($mediaPath);
        }

        return (bool) Model::deleteAllByColumn('section_id', $section->id());
    }

    /**
     * Install module.
     *
     * @return bool
     */
    public function install(): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg');
        Folder::create($mediaPath);

        if (!$this->database()->hasTable('mod_wysiwyg')) {
            $this->database()->exec('
                        CREATE TABLE `mod_wysiwyg` (
                            `wysiwyg_id` INT NOT NULL AUTO_INCREMENT,
                            `content` TEXT,
                            `section_id` INT NOT NULL,
                        PRIMARY KEY (`wysiwyg_id`));
                    ');
        }

        return true;
    }

    /**
     * Uninstall module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg');
        Folder::unlink($mediaPath, true);

        if ($this->database()->hasTable('mod_wysiwyg')) {
            return $this->database()->exec('DROP TABLE `mod_wysiwyg`');
        }

        return true;
    }

    /**
     * Initialize module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
        // Register service
        if (!$this->app()->get('services')->exist('wysiwyg')) {
            $this->app()->get('services')->set('wysiwyg', new Service());
        }

        return true;
    }

    /**
     * Update module.
     *
     * @return bool
     */
    public function update(): bool
    {
        if ('1.0' === $this->module->oldVersion) {
            $entity = new Model();
            $entity->entity_class = 'Neoflow\\Module\\WYSIWYG\\Model';
            $entity->validate();
            $entity->save();
        }

        return true;
    }
}
