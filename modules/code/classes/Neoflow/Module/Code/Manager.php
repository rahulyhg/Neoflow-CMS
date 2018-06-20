<?php

namespace Neoflow\Module\Code;

use Neoflow\CMS\Manager\AbstractPageModuleManager;
use Neoflow\CMS\Model\SectionModel;

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
        return (bool) Model::deleteAllByColumn('section_id', $section->id());
    }

    /**
     * Install module.
     *
     * @return bool
     */
    public function install(): bool
    {
        $this->database()->exec('
                    CREATE TABLE `mod_code` (
                        `code_id` INT NOT NULL AUTO_INCREMENT,
                        `content` TEXT,
                        `section_id` INT NOT NULL,
                    PRIMARY KEY (`code_id`),
                    INDEX `section_id_idx` (`section_id` ASC),
                    CONSTRAINT `fk_mod_code_section_id`
                        FOREIGN KEY (`section_id`)
                        REFERENCES `sections` (`section_id`)
                        ON DELETE CASCADE 
                        ON UPDATE NO ACTION)
                    ENGINE=InnoDB;
                ');

        return true;
    }

    /**
     * Uninstall module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        if ($this->database()->hasTable('mod_code')) {
            $this->database()->exec('DROP TABLE `mod_code`');
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
        if (!$this->app()->get('services')->exist('code')) {
            $this->app()->get('services')->set('code', new Service());
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
        return true;
    }
}
