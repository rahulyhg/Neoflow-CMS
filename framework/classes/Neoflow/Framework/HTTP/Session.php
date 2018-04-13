<?php

namespace Neoflow\Framework\HTTP;

use Neoflow\Framework\AppTrait;
use RuntimeException;

class Session
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var string
     */
    protected $sessionKey = '_session';

    /**
     * @var string
     */
    protected $flashKey = '_flash';

    /**
     * @var string
     */
    protected $flashDataNew = [];

    /**
     * @var bool
     */
    protected $reflash = false;

    /**
     * Constructor.
     *
     * @param string $name     Overwrite default PHP session name
     * @param int    $lifetime Overwrite default PHP session lifetime
     */
    public function __construct(string $name = '', int $lifetime = 0)
    {
        if ($name) {
            session_name($name);
        }

        session_set_cookie_params(null, '/', null, false, true);
        if ($lifetime) {
            ini_set('session.gc_maxlifetime', $lifetime);
        }
    }

    /**
     * Start session.
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function start()
    {
        if (session_start()) {
            if (isset($_SESSION['timeout_idle']) && $_SESSION['timeout_idle'] < time()) {
                session_destroy();
                session_start();
                session_regenerate_id();
            }
            $_SESSION['timeout_idle'] = time() + $this->config()->get('session')->get('lifetime');

            // Initialize session
            if (!isset($_SESSION[$this->sessionKey]) || !is_array($_SESSION[$this->sessionKey])) {
                $_SESSION[$this->sessionKey] = [];
            }

            // Initialize session flash
            if (!isset($_SESSION[$this->flashKey]) || !is_array($_SESSION[$this->flashKey])) {
                $_SESSION[$this->flashKey] = [];
            }

            register_shutdown_function(function () {
                $_SESSION[$this->flashKey] = $this->flashDataNew;
            });

            $this->logger()->debug('Session started', [
                'ID' => $this->getId(),
            ]);

            return $this;
        }
        throw new RuntimeException('Session start failed. Check PHP error log');
    }

    /**
     * Get session id.
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Destroy session.
     *
     * @return bool
     */
    public function destroy()
    {
        session_destroy();

        return true;
    }

    /**
     * Restart session.
     *
     * @return Session
     */
    public function restart()
    {
        if ($this->destroy()) {
            $this
                ->start()
                ->regenerateId();
        }

        return $this;
    }

    /**
     * Regenerate session id.
     *
     * @return self
     */
    public function regenerateId()
    {
        session_regenerate_id();

        return $this;
    }

    /**
     * Keep flash data for the next request.
     *
     * @return Session
     */
    public function reflash()
    {
        $this->flashDataNew = array_merge($_SESSION[$this->flashKey], $this->flashDataNew);
    }

    /**
     * Get session value.
     *
     * @param string $key Session key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (isset($_SESSION[$this->sessionKey][$key])) {
            return $_SESSION[$this->sessionKey][$key];
        }

        return;
    }

    /**
     * Check whether session value exist.
     *
     * @param string $key Session key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return isset($_SESSION[$this->sessionKey][$key]);
    }

    /**
     * Delete session value.
     *
     * @param string $key Session key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        if (isset($_SESSION[$this->sessionKey][$key])) {
            unset($_SESSION[$this->sessionKey][$key]);

            return true;
        }

        return false;
    }

    /**
     * Check whether session value exists.
     *
     * @param string $key Session key
     *
     * @return bool
     */
    public function has(string $key)
    {
        return isset($_SESSION[$this->sessionKey][$key]);
    }

    /**
     * Set session value.
     *
     * @param string $key   Session key
     * @param mixed  $value Session value
     *
     * @return Session
     */
    public function set(string $key, $value)
    {
        $_SESSION[$this->sessionKey][$key] = $value;

        return $this;
    }

    /**
     * Get session flash value.
     *
     * @param string $key Flash key
     *
     * @return mixed
     */
    public function getFlash(string $key)
    {
        if ($this->hasFlash($key)) {
            return $_SESSION[$this->flashKey][$key];
        }

        return;
    }

    /**
     * Check whether flash value exists.
     *
     * @param string $key Flash key
     *
     * @return bool
     */
    public function hasFlash(string $key)
    {
        return isset($_SESSION[$this->flashKey][$key]);
    }

    /**
     * Get new session flash value.
     *
     * @param string $key Flash key
     *
     * @return mixed
     */
    public function getNewFlash(striung $key)
    {
        if ($this->hasNewFlash($key)) {
            return $this->flashDataNew[$key];
        }

        return;
    }

    /**
     * Check whether new flash value exists.
     *
     * @param string $key Flash key
     *
     * @return bool
     */
    public function hasNewFlash(string $key)
    {
        return isset($this->flashDataNew[$key]);
    }

    /**
     * Delete new session flash value.
     *
     * @param string $key Flash key
     *
     * @return bool
     */
    public function deleteNewFlash(string $key)
    {
        if (isset($this->flashDataNew[$key])) {
            unset($this->flashDataNew[$key]);

            return true;
        }

        return false;
    }

    /**
     * Set new session flash value.
     *
     * @param string $key   Flash key
     * @param mixed  $value Flash value
     *
     * @return Session
     */
    public function setNewFlash(string $key, $value)
    {
        $this->flashDataNew[$key] = $value;

        return $this;
    }
}
