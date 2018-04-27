<?php

namespace Neoflow\Framework\HTTP\Responsing;

use InvalidArgumentException;

class JsonResponse extends Response
{
    /**
     * Constructor.
     *
     * @param array $data Content data
     */
    public function __construct(array $data = [])
    {
        $this
            ->setHeader('Content-type: application/json')
            ->setJson($data);
    }

    /**
     * Set JSON data.
     *
     * @param array $data JSON data
     *
     * @return self
     */
    public function setJson(array $data): self
    {
        return $this->setContent(json_encode($data));
    }

    /**
     * Set content.
     *
     * @param string $content Response content
     *
     * @return self
     */
    public function setContent(string $content): Response
    {
        if (is_json($content)) {
            return parent::setContent($content);
        }
        throw new InvalidArgumentException('Content is not valid JSON');
    }
}
