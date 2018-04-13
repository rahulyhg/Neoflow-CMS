<?php

namespace Neoflow\Module\WYSIWYG;

use Neoflow\CMS\Model\SectionModel;
use Neoflow\CMS\Manager\AbstractPageModuleManager;
use Neoflow\Filesystem\Folder;

class Manager extends AbstractPageModuleManager
{
    /**
     * Add WYSWYG module to section.
     *
     * @param SectionModel $section
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
                ])
                ->save();
    }

    /**
     * Remove WYSWIYG module from section.
     *
     * @param SectionModel $section
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
     * Install WYSIWYG module.
     *
     * @return bool
     */
    public function install(): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg');
        Folder::create($mediaPath);

        if (!$this->database()->hasTable('mod_wysiwyg')) {
            return $this
                    ->database()
                    ->prepare('CREATE TABLE `mod_wysiwyg` (
                                    `wysiwyg_id` INT NOT NULL AUTO_INCREMENT,
                                    `content` TEXT,
                                    `section_id` INT NOT NULL,
                                PRIMARY KEY (`wysiwyg_id`));')
                    ->execute();
        }

        return false;
    }

    /**
     * Uninstall WYSIWYG module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg');
        Folder::unlink($mediaPath, true);

        if ($this->database()->hasTable('mod_wysiwyg')) {
            return $this
                    ->database()
                    ->prepare('DROP TABLE `mod_wysiwyg`')
                    ->execute();
        }

        return true;
    }

    /**
     * Initialize WYSIWYG module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
        // Register service
        if (!$this->app()->hasService('wysiwyg')) {
            $this->app()->registerService(new Service(), 'wysiwyg');
        }

        return true;
    }

    /**
     * Update WYSIWYG module.
     *
     * @return bool
     */
    public function update(): bool
    {
        return true;
    }
}
