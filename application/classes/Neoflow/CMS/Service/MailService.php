<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\Mailer\Mail;

class MailService extends AbstractService
{
    /**
     * Create mail.
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     *
     * @return Mail
     */
    public function create(string $to, string $subject = '', string $message = ''): Mail
    {
        $from = $this->config()->get('app')->get('email');

        $mail = new Mail();

        return $mail
                ->setFrom($from)
                ->addTo($to)
                ->setSubject($subject)
                ->setMessage($message);
    }
}
