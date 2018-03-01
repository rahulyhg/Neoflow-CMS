<?php

namespace Neoflow\Mailer;

class Mail
{
    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var array
     */
    protected $cc = [];

    /**
     * @var array
     */
    protected $bcc = [];

    /**
     * @var array
     */
    protected $headers = array(
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/html; charset=UTF-8',
    );

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $from = '';

    /**
     * Add to email address.
     *
     * @param string $email
     *
     * @return self
     */
    public function addTo($email)
    {
        $this->to[] = $email;

        return $this;
    }

    /**
     * Set from email address.
     *
     * @param string $email
     *
     * @return self
     */
    public function setFrom($email)
    {
        $this->from = $email;

        return $this;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Add header.
     *
     * @param string $key
     * @param string $value
     *
     * @return self
     */
    public function addHeader($key, $value)
    {
        if ($key && $value) {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    /**
     * Add Cc email address.
     *
     * @param string $email
     *
     * @return self
     */
    public function addCc($email)
    {
        $this->cc[] = $email;

        return $this;
    }

    /**
     * Add Bcc email address.
     *
     * @param string $email
     *
     * @return self
     */
    public function addBcc($email)
    {
        $this->bcc = $email;

        return $this;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = '<html><body>'.nl2br($message).'</body></html>';

        return $this;
    }

    /**
     * Send mail.
     *
     * @return bool
     *
     * @throws MailException
     */
    public function send()
    {
        // Check for reciever or receivers
        if (!$this->to) {
            throw new MailException('Receiver email addresses not found');
        }

        if (!$this->from) {
            throw new MailException('Sender email address not found');
        }
        $this->addHeader('From', $this->from);

        // Add Cc to header
        $cc = implode(',', $this->cc);
        $this->addHeader('CC', $cc);

        // Add Bcc to header
        $bcc = implode(',', $this->bcc);
        $this->addHeader('BCC', $bcc);

        // Implode header to string
        $headers = array_map(function ($key, $value) {
            return $key.': '.$value;
        }, array_keys($this->headers), $this->headers);

        return mail(implode(',', $this->to), $this->subject, $this->message, implode("\r\n", $headers));
    }
}
