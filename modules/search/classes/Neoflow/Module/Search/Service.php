<?php
namespace Neoflow\Module\Search;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\Module\Search\Model\EntityModel;
use Neoflow\Module\Search\Model\SettingModel;

class Service extends AbstractService
{

    /**
     * @var SettingModel
     */
    protected $settings;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->settings = SettingModel::findById(1);
        $this->settings->setReadOnly();
    }

    /**
     * Register entity for search.
     *
     * @param string $entityClassName Class name of entity
     *
     * @return self
     */
    public function register(string $entityClassName): self
    {
        $entity = EntityModel::create([
                'entity_class' => $entityClassName
        ]);

        $entity->validate();
        $entity->save();

        return $this;
    }

    /**
     * Unregister entity for search.
     *
     * @param string $entityClassName Class name of entity
     *
     * @return bool
     */
    public function unregister(string $entityClassName): bool
    {
        return EntityModel::deleteAllByColumn('class_name', $entityClassName);
    }

    /**
     * Get search settings.
     *
     * @return SettingModel
     */
    public function getSettings(): SettingModel
    {
        return $this->settings;
    }

    public function search($query)
    {
        $results = new Results();

        $entities = EntityModel::findAll();
        foreach ($entities as $entity) {
            $entityResults = $entity::search($query);
            $results->addMultiple($entityResults);
        }

        $wysiwygs = \Neoflow\Module\WYSIWYG\Model::findAllByColumn('content', '%' . $query . '%', 'LIKE');

        foreach ($wysiwygs as $wysiwyg) {
            $page = $wysiwyg->getSection()->getPage();
            $content = strip_tags($wysiwyg->content);

            $position = mb_strpos($content, $query) - 3;
            if ($position < 0) {
                $position = 0;
            }
            $length = mb_strlen($query) + (3 * 2);

            $content = mb_substr($content, $position, $length);

            $results->add(new Result($page->getUrl(), $page->title, $content));
        }


        return $results;
    }
}
