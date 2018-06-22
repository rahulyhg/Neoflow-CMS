<?php

namespace Neoflow\Module\Blog;

use Neoflow\CMS\Manager\AbstractPageModuleManager;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Filesystem\Folder;
use Neoflow\Module\Blog\Model\ArticleModel;
use Neoflow\Module\Blog\Model\CategoryModel;
use Neoflow\Module\Blog\Model\SettingModel;

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

        SettingModel::deleteAllByColumn('section_id', $section->id());
        ArticleModel::deleteAllByColumn('section_id', $section->id());
        CategoryModel::deleteAllByColumn('section_id', $section->id());

        return true;
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
                      `article_id` INT NOT NULL AUTO_INCREMENT,
                      `section_id` INT NULL,
                      `title` VARCHAR(100) NOT NULL,
                      `title_slug` VARCHAR(100) NOT NULL,
                      `author_user_id` INT NULL,
                      `created_when` FLOAT NOT NULL DEFAULT 0,
                      `published_when` FLOAT NOT NULL DEFAULT 0,
                      `modified_when` FLOAT NOT NULL DEFAULT 0,
                      `abstract` VARCHAR(500) NULL,
                      `content` TEXT NULL,
                      `website_keywords` VARCHAR(250) NULL,
                      `website_description` VARCHAR(250) NULL,
                      `website_title` VARCHAR(100) NULL,
                      PRIMARY KEY (`article_id`),
                      UNIQUE INDEX `title_UNIQUE` (`title` ASC),
                      UNIQUE INDEX `title_slug_UNIQUE` (`title_slug` ASC),
                      INDEX `section_id_idx` (`section_id` ASC),
                      CONSTRAINT `fk_mod_blog_articles_section_id`
                        FOREIGN KEY (`section_id`)
                        REFERENCES `sections` (`section_id`)
                        ON DELETE CASCADE 
                        ON UPDATE NO ACTION,
                      INDEX `fk_mod_blog_articles_author_user_id_idx` (`author_user_id` ASC),
                      CONSTRAINT `fk_mod_blog_articles_author_user_id`
                        FOREIGN KEY (`author_user_id`)
                        REFERENCES `users` (`user_id`)
                        ON DELETE SET NULL
                        ON UPDATE NO ACTION)
                      ENGINE=InnoDB;

                    CREATE TABLE `mod_blog_categories` (
                      `category_id` INT NOT NULL AUTO_INCREMENT,
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
                        ON DELETE CASCADE
                        ON UPDATE NO ACTION)
                      ENGINE=InnoDB;
                        
                    CREATE TABLE `mod_blog_articles_categories` (
                      `article_category_id` INT NOT NULL AUTO_INCREMENT,
                      `article_id` INT NOT NULL,
                      `category_id` INT NOT NULL,
                      PRIMARY KEY (`article_category_id`),
                      INDEX `fk_mod_blog_articles_categories_article_id_idx` (`article_id` ASC),
                      INDEX `fk_mod_blog_articles_categories_category_id_idx` (`category_id` ASC),
                      CONSTRAINT `fk_mod_blog_articles_categories_article_id`
                        FOREIGN KEY (`article_id`)
                        REFERENCES `mod_blog_articles` (`article_id`)
                        ON DELETE CASCADE
                        ON UPDATE NO ACTION,
                      CONSTRAINT `fk_mod_blog_articles_categories_category_id`
                        FOREIGN KEY (`category_id`)
                        REFERENCES `mod_blog_categories` (`category_id`)
                        ON DELETE CASCADE
                        ON UPDATE NO ACTION)
                      ENGINE=InnoDB;
                        
                     CREATE TABLE `mod_blog_settings` (
                      `setting_id` INT NOT NULL AUTO_INCREMENT,
                      `section_id` INT NOT NULL,
                      `articles_per_page` INT NOT NULL DEFAULT 10,
                      PRIMARY KEY (`setting_id`),
                      INDEX `section_id_idx` (`section_id` ASC),
                      CONSTRAINT `fk_mod_blog_settings_section_id`
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
