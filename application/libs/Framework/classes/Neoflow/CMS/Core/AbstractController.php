<?php

namespace Neoflow\CMS\Core;

use Neoflow\CMS\AppTrait;
use Neoflow\Framework\Core\AbstractController as FrameworkAbstractController;

abstract class AbstractController extends FrameworkAbstractController
{
    /**
     * App trait.
     */
    use AppTrait;
}
