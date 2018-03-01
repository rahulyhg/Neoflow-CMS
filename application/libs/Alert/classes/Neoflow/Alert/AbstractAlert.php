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
     * @param string|array $message
     */
    public function __construct($message)
    {
        if (is_array($message)) {
            $this->setMessages($message);
        } else {
            $this->addMessage($message);
        }
    }

    /**
     * Set messages.
     *
     * @param array $messages
     *
     * @return self
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Add message.
     *
     * @param string $message
     *
     * @return self
     */
    public function addMessage(string $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Get alert type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
