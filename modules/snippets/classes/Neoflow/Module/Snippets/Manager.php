<?php

namespace Neoflow\Module\Snippets;

use Neoflow\CMS\Manager\AbstractModuleManager;

class Manager extends AbstractModuleManager
{
    /**
     * Install module.
     *
     * @return bool
     */
    public function install(): bool
    {
        if (!$this->database()->hasTable('mod_snippets')) {
            $this
                ->database()
                ->prepare('
                        CREATE TABLE `mod_snippets` (
                            `code_id` INT NOT NULL AUTO_INCREMENT,
                            `title` VARCHAR(100) NOT NULL,
                            `code` TEXT NOT NULL,
                            `placeholder` VARCHAR(100) NOT NULL,
                            `description` TEXT NOT NULL,
                        PRIMARY KEY (`code_id`));

                        INSERT INTO `mod_snippets` VALUES(1, "Dummy", "return \"Just a snippet\";", "dummy", "Dummy snippet....", "");
                        INSERT INTO `mod_snippets` VALUES(2, "Google Analytics", "return \"<script>\r\n	(function (i, s, o, g, r, a, m) {\r\n		i[\"GoogleAnalyticsObject\"] = r;\r\n		i[r] = i[r] || function () {\r\n			(i[r].q = i[r].q || []).push(arguments)\r\n		}, i[r].l = 1 * new Date();\r\n		a = s.createElement(o),\r\n				m = s.getElementsByTagName(o)[0];\r\n		a.async = 1;\r\n		a.src = g;\r\n		m.parentNode.insertBefore(a, m);\r\n	})(window, document, \"script\", \"https://www.google-analytics.com/analytics.js\", \"ga\");\r\n\r\n	ga(\"create\", \"\" . $id . \"\", \"auto\");\r\n	ga(\"send\", \"pageview\");\r\n</script>\";", "GoogleAnalytics", "Creates the JavaScript code for Google Analytics based on an custom ID which passed by a parameter.", "id");
                        ')
                ->execute();
        }

        return false;
    }

    /**
     * Uninstall module.
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
     * Execute module.
     *
     * @return bool
     */
    public function execute(): bool
    {
        if ('frontend' === $this->app()->get('area')) {
            $response = $this->app()->get('response');

            if ($response) {
                $content = preg_replace_callback('/\[\[([\w\d]+)\??(.*)\]\]/i', function ($matches) {
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
                }, $response->getContent());

                $response->setContent($content);
            } else {
                return false;
            }
        }

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
