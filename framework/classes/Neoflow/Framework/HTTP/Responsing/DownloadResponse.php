<?php

namespace Neoflow\Framework\HTTP\Responsing;

use Neoflow\Filesystem\File;

class DownloadResponse extends Response
{
    /**
     * @var File
     */
    protected $file;

    /**
     * Constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath = '')
    {
        if ($filePath) {
            $file = new File($filePath);
            $this->setFile($file);
        }
    }

    /**
     * Set download file.
     *
     * @param File $file
     *
     * @return self
     */
    public function setFile(File $file): self
    {
        $this
            ->setHeader('Content-Type: '.$file->getMimeContentType())
            ->setHeader('Content-Description: File Transfer')
            ->setHeader('Content-Disposition: attachment; filename="'.$file->getName().'"')
            ->setHeader('Expires: 0')
            ->setHeader('Cache-Control: must-revalidate')
            ->setHeader('Pragma: public')
            ->setHeader('Content-Length: '.$file->getSize());

        $this->file = $file;

        return $this;
    }

    /**
     * Send download resonse.
     */
    public function sendFile(): void
    {
        $this->sendHeader();
        readfile($this->file->getPath());
        $this->isSent = true;
        $this->logger()->info('Download response sent');
    }

    /**
     * Send response.
     */
    public function send(): void
    {
        $this->sendFile();
    }
}
