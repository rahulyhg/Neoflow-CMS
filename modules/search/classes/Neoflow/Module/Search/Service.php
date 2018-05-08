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
                'entity_class' => $entityClassName,
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

    /**
     * Search results.
     *
     * @param string $query Search query string
     *
     * @return Results
     */
    public function search(string $query): Results
    {
        $results = new Results();

        $query = str_replace(['*', ' '], '%', $query);

        $entities = EntityModel::findAll();
        foreach ($entities as $entity) {
            if (class_exists($entity->entity_class)) {
                $entityResults = $entity->entity_class::search($query);
                $results->addMultiple($entityResults->toArray());
            }
        }

        return $results;
    }
}
