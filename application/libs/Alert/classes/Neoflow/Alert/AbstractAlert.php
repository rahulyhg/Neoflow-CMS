<?php
namespace Neoflow\Alert;

abstract class AbstractAlert
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * Constructor.
     *
     * @param string|array $messages One or multiple messages
     */
    public function __construct($messages)
    {
        $this->setMessages($messages);
    }

    /**
     * Set messages.
     *
     * @param string|array $messages One or multiple messages
     *
     * @return self
     */
    public function setMessages($messages): self
    {
        if (is_string($messages)) {
            $this->messages[] = $messages;
        } else {
            $this->messages = $messages;
        }

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
