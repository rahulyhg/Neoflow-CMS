<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;

class AlertService extends AbstractService {

    /**
     * Check whether alerts exists.
     *
     * @return bool
     */
    public function hasAlerts(): bool {
        return (bool) count($this->getAlerts());
    }

    /**
     * Get alert.
     *
     * @return array
     */
    public function getAlerts(): array {
        return $this->get('alerts', []);
    }

    /**
     * Set alert.
     *
     * @param AbstractAlert $alert Alert
     * @param bool          $asFlash
     *
     * @return self
     */
    protected function set(AbstractAlert $alert, bool $asFlash = true): self {
        if ($asFlash) {
            $alerts = [];
            if ($this->session()->hasNewFlash('alerts')) {
                $alerts = $this->session()->getNewFlash('alerts');
            }
            $alerts[] = $alert;
            $this->session()->setNewFlash('alerts', $alerts);
        } else {
            $alerts = $this->get('alerts');
            $alerts[] = $alert;
            $this->set('alerts', $alerts);
        }

        return $this;
    }

    /**
     * Create danger alert
     *
     * @param string|array $message Message or a list of messages
     * @param string $presentationType Alert presentation type (now, next (next request), forever
     *
     * @return self
     */
    public function danger($message, bool $presentationType = 'flash'): self {
        return $this->set(new DangerAlert($message), $presentationType);
    }

    /**
     * Create info alert and set as session flash.
     *
     * @param string|array $message Message or a list of messages
     *
     * @return self
     */
    public function setInfoAlert($message, bool $asFlash = true): self {
        return $this->setAlert(new InfoAlert($message), $asFlash);
    }

    /**
     * Create success alert and set as session flash.
     *
     * @param string|array $message Message or a list of messages
     *
     * @return self
     */
    public function setSuccessAlert($message, bool $asFlash = true): self {
        return $this->setAlert(new SuccessAlert($message), $asFlash);
    }

    /**
     * Create warning alert and set as session flash.
     *
     * @param string|array $message Message or a list of messages
     *
     * @return self
     */
    public function setWarningAlert($message, bool $asFlash = true): self {
        return $this->setAlert(new WarningAlert($message), $asFlash);
    }

}
