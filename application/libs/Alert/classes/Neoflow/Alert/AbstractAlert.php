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
     * @param string|array $message Message or a list of messages
     */
    public function __construct($message)
    {
        if (is_string($message)) {
            $this->addMessage($message);
        } else {
            $this->messages = $message;
        }

        return $this;
    }

    /**
     * Add message.
     *
     * @param string $message Message
     *
     * @return self
     */
    public function addMessage(string $message): self
    {
        $this->messages[] = $message;

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
