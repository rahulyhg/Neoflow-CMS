<?php

namespace Neoflow\Module\Blog;

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
        $mediaPath = $this->config()->getMediaPath('/modules/blog/section-'.$section->id());
        if (!is_dir($mediaPath)) {
            Folder::create($mediaPath);
        }

        return (bool) SettingModel::create([
            'section_id' => $section->id(),
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
        $mediaPath = $this->config()->getMediaPath('/modules/blog/section-'.$section->id());
        if (is_dir($mediaPath)) {
            Folder::unlink($mediaPath);
        }

        return (bool) SettingModel::deleteByColumn('section_id', $section->id());
    }

    /**
     * Install module.
     *
     * @return bool
     */
    public function install(): bool
    {
        $mediaPath = $this->config()->getMediaPath('/modules/blog');
        Folder::create($mediaPath);

        $this->database()->exec('
                    CREATE TABLE `mod_blog_articles` (
                      `blog_id` INT NOT NULL AUTO_INCREMENT,
                      `section_id` INT NULL,
                      `title` VARCHAR(100) NOT NULL,
                      `title_slug` VARCHAR(100) NOT NULL,
                      `abstract` VARCHAR(500) NULL,
                      `content` TEXT NULL,
                      `website_keywords` VARCHAR(250) NULL,
                      `website_description` VARCHAR(250) NULL,
                      `website_title` VARCHAR(100) NULL,
                      PRIMARY KEY (`blog_id`),
                      UNIQUE INDEX `title_UNIQUE` (`title` ASC),
                      UNIQUE INDEX `title_slug_UNIQUE` (`title_slug` ASC),
                      INDEX `section_id_idx` (`section_id` ASC),
                      CONSTRAINT `fk_mod_blog_articles_section_id`
                        FOREIGN KEY (`section_id`)
                        REFERENCES `sections` (`section_id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION);

                    CREATE TABLE `mod_blog_categories` (
                      `category_id` INT NOT NULL,
                      `section_id` INT NOT NULL,
                      `title` VARCHAR(100) NOT NULL,
                      `title_slug` VARCHAR(100) NOT NULL,
                      `description` VARCHAR(250) NULL,
                      `website_keywords` VARCHAR(250) NULL,
                      `website_description` VARCHAR(250) NULL,
                      `website_title` VARCHAR(100) NULL,
                      PRIMARY KEY (`category_id`),
                      UNIQUE INDEX `title_slug_UNIQUE` (`title_slug` ASC),
                      UNIQUE INDEX `title_UNIQUE` (`title` ASC),
                      INDEX `section_id_idx` (`section_id` ASC),
                      CONSTRAINT `fk_mod_blog_categories_section_id`
                        FOREIGN KEY (`section_id`)
                        REFERENCES `sections` (`section_id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION);
                        
                     CREATE TABLE `mod_blog_settings` (
                      `setting_id` INT NOT NULL,
                      `section_id` INT NOT NULL,
                      `articles_per_page` INT NOT NULL DEFAULT 10,
                      PRIMARY KEY (`setting_id`),
                      INDEX `section_id_idx` (`section_id` ASC),
                      CONSTRAINT `fk_mod_blog_settings_section_id`
                        FOREIGN KEY (`section_id`)
                        REFERENCES `sections` (`section_id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION);
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
        $mediaPath = $this->config()->getMediaPath('/modules/blog');
        if (is_dir($mediaPath)) {
            Folder::unlink($mediaPath, true);
        }

        $this->database()->exec('
                    DROP TABLE `mod_blog_articles`;
                    DROP TABLE `mod_blog_categories`;
                    DROP TABLE `mod_blog_settings`;
                ');

        return true;
    }

    /**
     * Initialize module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
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
