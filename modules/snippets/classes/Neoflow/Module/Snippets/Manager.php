<?php
namespace Neoflow\Module\Snippets;

use Neoflow\CMS\Manager\AbstractModuleManager;

class Manager extends AbstractModuleManager
{

    /**
     * Install Snippets module.
     *
     * @return bool
     */
    public function install(): bool
    {
        if (!$this->database()->hasTable('mod_snippets')) {
            return $this
                    ->database()
                    ->prepare('CREATE TABLE `mod_snippets` (
                                    `code_id` INT NOT NULL AUTO_INCREMENT,
                                    `title` VARCHAR(100) NOT NULL,
                                    `code` TEXT NOT NULL,
                                    `placeholder` VARCHAR(100) NOT NULL,
                                    `description` TEXT NOT NULL,
                                PRIMARY KEY (`code_id`));')
                    ->execute();
        }

        return false;
    }

    /**
     * Uninstall Snippets module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        if ($this->database()->hasTable('mod_snippets')) {
            return $this
                    ->database()
                    ->prepare('DROP TABLE `mod_snippets`')
                    ->execute();
        }

        return true;
    }

    /**
     * Execute Snippets module.
     *
     * @return bool
     */
    public function execute(): bool
    {
        $response = $this->app()->get('response');

        $content = $response->getContent();

        $response->setContent(preg_replace_callback('/\[\[([\w\d]+)\??(.*)\]\]/i', function ($matches) {
                $snippet = Model::findByColumn('placeholder', $matches[1]);

                if ($snippet) {
                    $parameters = [];
                    if (isset($matches[2])) {
                        mb_parse_str($matches[2], $parameters);
                    }

                    $code = $snippet->executeCode($parameters);

                    return $code;
                }

                return '';
            }, $content));

        return true;
    }

    /**
     * Update Snippets module.
     *
     * @return bool
     */
    public function update(): bool
    {
        return true;
    }
}
