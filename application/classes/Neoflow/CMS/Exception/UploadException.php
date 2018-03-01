<?php

namespace Neoflow\CMS\Exception;

use Exception;
use Throwable;

class UploadException extends Exception
{
    /**
     * Constructor.
     *
     * @param string    $fileName
     * @param int       $code
     * @param Throwable $previous
     */
    public function __construct(string $fileName = '', int $code = -1, Throwable $previous = null)
    {
        $message = $this->getMessageByCode($code);

        $message = str_replace('{0}', $fileName, $message);

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get message by code.
     *
     * @param int $code
     *
     * @return string
     */
    protected function getMessageByCode(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file "{0}" exceeds the UPLOAD_MAX_FILESIZE directive in php.ini';

            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file "{0}" exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';

            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file "{0}" was only partially uploaded';

            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';

            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';

            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';

            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';

            default:
                return 'Unknown upload error';
        }
    }
}
