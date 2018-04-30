<?php

namespace Neoflow\Framework\HTTP\Responsing;

use Neoflow\Filesystem\File;

class StreamResponse extends Response
{
    /**
     * @var resource
     */
    protected $filePointer;

    /**
     * Constructor.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->filePointer = fopen($file->getPath(), 'r');
        if ($file->getSize() > 0) {
            $this->setHeader('Content-Type: '.$file->getMimeContentType());
        } else {
            $this->setHeader('Content-Type: text/plain');
        }
    }

    /**
     * Stream response.
     */
    public function stream()
    {
        $this->sendHeader();
        fpassthru($this->filePointer);
        $this->isSent = true;
        $this->logger()->info('Response streamed');
    }

    /**
     * Send response.
     */
    public function send()
    {
        $this->stream();
    }
}
