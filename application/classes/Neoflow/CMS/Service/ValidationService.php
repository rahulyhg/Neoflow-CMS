<?php
namespace Neoflow\CMS\Service;

use Neoflow\Framework\Core\AbstractService;

class ValidationService extends AbstractService
{

    /**
     * Check whether validation error exists.
     *
     * @param string $key Validiation error key
     *
     * @return bool
     */
    public function hasError(string $key = ''): bool
    {
        $validationErrors = $this->session()->getFlash('validationErrors');

        return is_array($validationErrors) && ('' === $key || isset($validationErrors[$key]));
    }

    /**
     * Get validated data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->session()->getFlash('validationData') ?: [];
    }
}
