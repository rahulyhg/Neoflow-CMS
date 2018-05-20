<?php

namespace Neoflow\CMS\Service;

use InvalidArgumentException;
use Neoflow\Alert\AbstractAlert;
use Neoflow\Alert\DangerAlert;
use Neoflow\Alert\InfoAlert;
use Neoflow\Alert\SuccessAlert;
use Neoflow\Alert\WarningAlert;
use Neoflow\CMS\Core\AbstractService;

class AlertService extends AbstractService
{
    /**
     * @var array
     */
    protected $alerts = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $sessionAlerts = $this->session()->get('alerts');
        if (!empty($sessionAlerts)) {
            $this->alerts = $sessionAlerts;
        }

        $flashAlerts = $this->session()->getFlash('alerts');
        if (!empty($flashAlerts)) {
            $this->alerts = array_merge($this->alerts, $flashAlerts);
        }
    }

    /**
     * Count alerts for current request.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->alerts);
    }

    /**
     * Get all alerts for current request.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->alerts;
    }

    /**
     * Set alert.
     *
     * @param AbstractAlert $alert        Alert
     * @param string        $presentation Presentation time (now, next (next request), forever)
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    protected function set(AbstractAlert $alert, string $presentation = 'next'): self
    {
        if (in_array($presentation, ['next', 'forever', 'now'])) {
            $alerts = [];
            switch ($presentation) {
                case 'forever':
                    if ($this->session()->has('alerts')) {
                        $alerts = $this->session()->get('alerts');
                    }
                    $alerts[] = $alert;
                    $this->session()->set('alerts', $alerts);
                    break;
                case 'now':
                    $this->alerts[] = $alert;
                    break;
                case 'next':
                    if ($this->session()->hasNewFlash('alerts')) {
                        $alerts = $this->session()->getNewFlash('alerts');
                    }
                    $alerts[] = $alert;
                    $this->session()->setNewFlash('alerts', $alerts);
            }

            return $this;
        }
        throw new InvalidArgumentException('Presentation time "'.$presentation.'" is invalid (need to be "next", "now" or "forever)');
    }

    /**
     * Create and set danger alert.
     *
     * @param string|array $message      Message or a list of messages
     * @param string       $presentation Presentation time (now, next (next request), forever)
     *
     * @return self
     */
    public function danger($message, string $presentation = 'next'): self
    {
        return $this->set(new DangerAlert($message), $presentation);
    }

    /**
     * Create and set info alert.
     *
     * @param string|array $message      Message or a list of messages
     * @param string       $presentation Presentation time (now, next (next request), forever)
     *
     * @return self
     */
    public function info($message, string $presentation = 'next'): self
    {
        return $this->set(new InfoAlert($message), $presentation);
    }

    /**
     * Create and set success alert.
     *
     * @param string|array $message      Message or a list of messages
     * @param string       $presentation Presentation time (now, next (next request), forever)
     *
     * @return self
     */
    public function success($message, string $presentation = 'next'): self
    {
        return $this->set(new SuccessAlert($message), $presentation);
    }

    /**
     * Create and set warning alert.
     *
     * @param string|array $message      Message or a list of messages
     * @param string       $presentation Presentation time (now, next (next request), forever)
     *
     * @return self
     */
    public function warning($message, string $presentation = 'next'): self
    {
        return $this->set(new WarningAlert($message), $presentation);
    }
}
