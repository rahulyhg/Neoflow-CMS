<?php
namespace Neoflow\CMS\Service;

use Neoflow\Framework\Core\AbstractService;

class ValidationService extends AbstractService
{

    /**
     * Check whether validation error exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasError($key = '')
    {
        $validationErrors = $this->session()->getFlash('validationErrors');

        return is_array($validationErrors) && ('' === $key || isset($validationErrors[$key]));
    }

    /**
     * Get validated data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->session()->getFlash('validationData') ?: [];
    }
}
