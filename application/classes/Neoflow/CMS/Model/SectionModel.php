<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\CMS\View\FrontendView;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use RuntimeException;

class SectionModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'sections';

    /**
     * @var string
     */
    public static $primaryKey = 'section_id';

    /**
     * @var array
     */
    public static $properties = ['section_id', 'page_id', 'module_id',
        'position', 'block_id', 'is_active', ];

    /**
     * Get repository to fetch page.
     *
     * @return Repository
     */
    public function page()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PageModel', 'page_id');
    }

    /**
     * Render frontend output based on the referenced page module.
     *
     * @param FrontendView $view
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function render(FrontendView $view)
    {
        // Get module
        $module = $this->module()->fetch();

        if ($module) {
            $routing = [];
            if ($this->app()->get('module_url')) {
                // Define URL params
                $url = '{url:'.$module->identifier.'}'.$this->app()->get('module_url');
                $httpMethod = $this->request()->getHttpMethod();

                // Get routing
                $routing = $this->router()->getRoutingByUrl($url, $httpMethod);
            }

            if (isset($routing['route'][0]) && $routing['route'][0] !== 'frontend_index') {
                $args = array_merge($routing['args'], ['section' => $this]);
                $this->router()->route($routing['route'], $args, $view);
                $this->app()->set('module_url_routed', true);
            } else {
                $this->router()->routeByKey($module->frontend_route, ['section' => $this], $view);
            }

            // Render block
            $content = $this->engine()->renderBlock('section-content');

            // Reset
            $this->engine()->unsetBlock('section-content');

            return $content;
        }
        throw new RuntimeException('Cannot render section');
    }

    /**
     * Get repository to fetch module.
     *
     * @return ModuleModel
     */
    public function module()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\ModuleModel', 'module_id');
    }

    /**
     * Get repository to fetch block.
     *
     * @return BlockModel
     */
    public function block()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\BlockModel', 'block_id');
    }

    /**
     * Save section.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        if (0 == $this->block_id) {
            $this->block_id = null;
        }

        if (!$this->position) {
            $this->position = 1;
            $page = $this->page()->fetch();
            $lastSection = $page->sections()
                    ->orderByDesc('position')
                    ->fetch();

            if ($lastSection) {
                $this->position = $lastSection->position + 1;
            }
        }

        if (parent::save($preventCacheClearing)) {
            if ($this->isNew) {
                $module = $this->module()->fetch();

                return $module->getManager()->add($this);
            }

            return true;
        }

        return false;
    }

    /**
     * Delete section.
     *
     * @return bool
     */
    public function delete()
    {
        $module = $this->module()->fetch();
        if ($module && $module->getManager()->remove($this)) {
            return parent::delete();
        }
    }

    /**
     * Validate section.
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new EntityValidator($this);

        $validator
                ->required()
                ->set('module_id', 'Module');

        return $validator->validate();
    }

    /**
     * Toggle activation.
     *
     * @return self
     */
    public function toggleActivation()
    {
        if ($this->is_active) {
            $this->is_active = false;
        } else {
            $this->is_active = true;
        }

        return $this;
    }
}
