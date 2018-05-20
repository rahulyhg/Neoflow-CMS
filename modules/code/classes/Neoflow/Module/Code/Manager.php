<?php

namespace Neoflow\Module\Code;

use Neoflow\CMS\Model\SectionModel;
use Neoflow\CMS\Manager\AbstractPageModuleManager;

class Manager extends AbstractPageModuleManager
{
    /**
     * Add Code module to section.
     *
     * @param SectionModel $section
     *
     * @return bool
     */
    public function add(SectionModel $section): bool
    {
        return (bool) Model::create([
                            'section_id' => $section->id(),
                            'content' => '',
                        ])
                        ->save();
    }

    /**
     * Remove Code module from section.
     *
     * @param SectionModel $section
     *
     * @return bool
     */
    public function remove(SectionModel $section): bool
    {
        return (bool) Model::deleteAllByColumn('section_id', $section->id());
    }

    /**
     * Install Code module.
     *
     * @return bool
     */
    public function install(): bool
    {
        $sqlFilePath = $this->module->getPath('install.sql');

        return (bool) $this->database()->executeFile($sqlFilePath);
    }

    /**
     * Uninstall Code module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        if ($this->database()->hasTable('mod_code')) {
            return $this
                            ->database()
                            ->exec('DROP TABLE `mod_code`');
        }

        return true;
    }

    /**
     * Initialize Code module.
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
     * Update Code module.
     *
     * @return bool
     */
    public function update(): bool
    {
        // Nothing todo
        return true;
    }
}
