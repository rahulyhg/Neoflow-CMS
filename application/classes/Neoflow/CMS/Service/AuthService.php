<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Exception\AuthException;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class AuthService extends AbstractService
{
    /**
     * Authenticate and authorize user by email address and password.
     *
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public function login($email, $password)
    {
        if ($this->authenticate($email, $password)) {
            return $this->authorize();
        }

        return false;
    }

    /**
     * Logout authenticated user.
     *
     * @return bool
     */
    public function logout()
    {
        $this->session()->restart();

        return true;
    }

    /**
     * Check whether a user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->session()->exists('_USER');
    }

    /**
     * Get authenticated user.
     *
     * @return UserModel
     */
    public function getUser()
    {
        return $this->session()->get('_USER');
    }

    /**
     * Create reset key for user.
     *
     * @param string $email
     * @param bool   $sendMail
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function createResetKey(string $email, bool $sendMail = true)
    {
        $user = UserModel::repo()
            ->where('email', '=', $email)
            ->fetch();

        if ($user) {
            if (!$user->reset_key || 1 === 1 || $user->reseted_when < microtime(true) - 60 * 60) {
                if ($user->generateResetKey() && $user->save()) {
                    $link = generate_url('backend_new_password', [
                        'reset_key' => $user->reset_key,
                    ]);
                    $message = translate('Password reset email message', [$user->getFullName(), $link]);
                    $subject = translate('Password reset email subject');

                    if ($sendMail) {
                        return $this
                                ->getService('mail')
                                ->create($user->email, $subject, $message)
                                ->send();
                    }

                    return true;
                }
            } else {
                throw new AuthException(translate('Email already sent, you can reset your password once per hour'));
            }
        } else {
            throw new AuthException(translate('User not found'));
        }
    }

    /**
     * Check whether authenticated user has permission.
     *
     * @param string|array $permissionKeys
     *
     * @return bool
     */
    public function hasPermission($permissionKeys)
    {
        if (is_string($permissionKeys)) {
            $permissionKeys = [$permissionKeys];
        }

        return array_in_array($permissionKeys, $this->getPermissionKeys());
    }

    /**
     * Authenticate user with email and password.
     *
     * @param string $email
     * @param string $password
     * @param bool   $authorize
     *
     * @return bool
     */
    protected function authenticate($email, $password): bool
    {
        $user = UserModel::repo()
            ->caching(false)
            ->where('email', '=', $email)
            ->fetch();

        if ($user) {
            if ($user->failed_logins <= $this->settings()->login_attempts) {
                if ($user->verifyPassword($password)) {
                    $user->failed_logins = 0;
                    $user->save(true);
                    $user->setReadOnly();
                    $this->session()->set('_USER', $user);

                    return true;
                }
            } else {
                throw new AuthException(translate('Too much login attempts than allowed. Your user account is locked.'));
            }

            $user->failed_logins += 1;
            $user->save(true);

            throw new AuthException(translate('Login failed. You have {0} login attempts until your user account get locked.', [$this->settings()->login_attempts - $user->failed_logins]));
        }

        throw new AuthException(translate('Login failed. Email address and/or password are invalid.'));
    }

    /**
     * Authorize authenticated user.
     *
     * @param UserModel $user
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    protected function authorize()
    {
        $user = $this->getUser();

        if ($user) {
            $role = $user
                ->role()
                ->fetch();

            $permissions = $role
                ->permissions()
                ->fetchAll();

            $permissionKeys = $permissions->map(function ($permission) {
                return $permission->permission_key;
            });

            $this->session()
                ->regenerateId()
                ->set('_PERMISSION_KEYS', $permissionKeys);

            return true;
        }
        throw new RuntimeException('Authentication failed');
    }

    /**
     * Get permission keys of authenticated user.
     *
     * @return array
     */
    protected function getPermissionKeys()
    {
        return $this->session()->get('_PERMISSION_KEYS') ?: [];
    }
}
