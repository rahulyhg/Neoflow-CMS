<?php
namespace Neoflow\CMS\Service;

use InvalidArgumentException;
use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Exception\UploadException;
use Neoflow\Filesystem\File;
use Neoflow\Validation\ValidationException;

class UploadService extends AbstractService
{

    /**
     * Move multiple uploaded file items to directory.
     *
     * @param string $uploadedItems         Uploaded POST file items
     * @param string $directoryPath         Target directory path to move the uploaded file item
     * @param bool   $overwrite             Set FALSE to prevent overwriting, when a file with same name in the new directory path already exist
     * @param array  $allowedFileExtensions List of allowed file extensions
     * @param int    $allowedFileSize       Allowed file size in bytes (-1 = unlimited)
     *
     * @return array
     */
    public function moveMultiple(array $uploadedItems, string $directoryPath, bool $overwrite = true, array $allowedFileExtensions = [], int $allowedFileSize = -1): array
    {
        $result = [
            'success' => [],
            'error' => [],
        ];
        foreach ($uploadedItems as $uploadedItem) {
            try {
                $result['success'][$uploadedItem['name']] = $this->move($uploadedItem, $directoryPath, $overwrite, $allowedFileExtensions);
            } catch (ValidationException $ex) {
                $result['error'][$uploadedItem['name']] = $ex->getMessage();
            }
        }

        return $result;
    }

    /**
     * Move uploaded file item to directory.
     *
     * @param string $uploadedItem          Uploaded POST file item
     * @param string $directoryPath         Target directory path to move the uploaded file item
     * @param bool   $overwrite             Set FALSE to prevent overwriting, when a file with same name in the new directory path already exist
     * @param array  $allowedFileExtensions List of allowed file extensions
     * @param int    $allowedFileSize       Allowed file size in bytes (-1 = unlimited)
     *
     * @return File
     *
     * @throws UploadException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function move(array $uploadedItem, string $directoryPath, bool $overwrite = true, array $allowedFileExtensions = [], int $allowedFileSize = -1): File
    {
        if (is_dir($directoryPath) && is_valid_path($directoryPath)) {
            if (isset($uploadedItem['tmp_name']) && isset($uploadedItem['name']) && isset($uploadedItem['error'])) {
                if ($uploadedItem['error'] > 0) {
                    throw new UploadException($uploadedItem['name'], $uploadedItem['error']);
                }

                $uploadedFileExtension = pathinfo($uploadedItem['name'], PATHINFO_EXTENSION);
                if (count($allowedFileExtensions) > 0 && '' !== $uploadedFileExtension && !in_array($uploadedFileExtension, $allowedFileExtensions)) {
                    throw new ValidationException(translate('The file "{0}" has not an allowed file extension', [$uploadedItem['name']]));
                }
                if ($allowedFileSize >= 0 && $allowedFileSize <= $uploadedItem['size']) {
                    throw new ValidationException(translate('The file "{0}" is larger than allowed', [$uploadedItem['name']]));
                }

                $uploadedFilePath = normalize_path($directoryPath . '/' . $uploadedItem['name']);
                if ($overwrite || !is_file($uploadedFilePath)) {
                    move_uploaded_file($uploadedItem['tmp_name'], $uploadedFilePath);

                    return File::load($uploadedFilePath);
                }
                throw new ValidationException(translate('A file with the name "{0}" already exists', [$uploadedItem['name']]));
            }
            throw new InvalidArgumentException('Target directory is invalid');
        }
        throw new InvalidArgumentException('Uploaded file item is not a valid POST file array');
    }
}
