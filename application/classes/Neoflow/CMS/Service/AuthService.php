<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Exception\AuthException;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use function array_in_array;
use function generate_url;
use function translate;

class AuthService extends AbstractService
{
    /**
     * Authenticate and authorize user by email address and password.
     *
     * @param string $email    User email address
     * @param string $password User password
     *
     * @return bool
     *
     * @throws AuthException
     */
    public function login(string $email, string $password): bool
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
    public function logout(): bool
    {
        $this->session()->restart();

        return true;
    }

    /**
     * Check whether a user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->session()->exists('_USER');
    }

    /**
     * Get authenticated user.
     *
     * @return UserModel|null
     */
    public function getUser()
    {
        return $this->session()->get('_USER');
    }

    /**
     * Create reset key for user.
     *
     * @param string $email    User email address
     * @param bool   $sendMail Set FALSE to prevent sending an email with password reset link
     *
     * @return bool
     *
     * @throws AuthException
     */
    public function createResetKey(string $email, bool $sendMail = true): bool
    {
        $user = UserModel::repo()->where('email', '=', $email)->fetch();

        if ($user) {
            if (!$user->reset_key || $user->reseted_when < microtime(true) - 60 * 60) {
                if ($user->generateResetKey() && $user->save()) {
                    $link = generate_url('backend_auth_new_password', ['reset_key' => $user->reset_key]);
                    $message = translate('Password reset email message', [$user->getFullName() ?: $user->email, $link]);
                    $subject = translate('Password reset email subject');

                    if ($sendMail) {
                        return $this->service('mail')->create($user->email, $subject, $message)->send();
                    }

                    return true;
                }
            }
            throw new AuthException(translate('Email already sent, you can reset your password once per hour.'));
        }
        throw new AuthException(translate('User for password reset not found.'));
    }

    /**
     * Update password of user by reset key.
     *
     * @param string $newPassword     New password
     * @param string $confirmPassword Confirm password
     * @param string $resetKey        Reset key
     *
     * @return bool
     *
     * @throws AuthException
     * @throws ValidationException
     */
    public function updatePasswordByResetKey(string $newPassword, string $confirmPassword, string $resetKey)
    {
        $user = UserModel::findByColumn('reset_key', $resetKey);

        if ($user) {
            $user->setNewPassword($newPassword, $confirmPassword)->deleteResetKey();

            // Validate and save user password
            return $user->validateNewPassword() && $user->save();
        }
        throw new AuthException(translate('User for password reset not found.'));
    }

    /**
     * Check whether authenticated user has permission.
     *
     * @param string|array $permissionKeys One or multiple permission keys
     *
     * @return bool
     */
    public function hasPermission($permissionKeys): bool
    {
        if (is_string($permissionKeys)) {
            $permissionKeys = [$permissionKeys];
        }

        return array_in_array($permissionKeys, $this->getPermissionKeys());
    }

    /**
     * Authenticate user with email and password.
     *
     * @param string $email    Email address
     * @param string $password User password
     *
     * @return bool
     *
     * @throws AuthException
     */
    protected function authenticate($email, $password): bool
    {
        $user = UserModel::repo()->caching(false)->where('email', '=', $email)->fetch();

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

            ++$user->failed_logins;
            $user->save(true);

            throw new AuthException(translate('Login failed. You have {0} login attempts until your user account get locked.', [$this->settings()->login_attempts - $user->failed_logins]));
        }

        throw new AuthException(translate('Login failed. Email address and/or password are invalid.'));
    }

    /**
     * Authorize authenticated user.
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    protected function authorize(): bool
    {
        $user = $this->getUser();

        if ($user) {
            $role = $user->role()->fetch();

            $permissions = $role->permissions()->fetchAll();

            $permissionKeys = $permissions->map(function ($permission) {
                return $permission->permission_key;
            });

            $this->session()->regenerateId()->set('_PERMISSION_KEYS', $permissionKeys);

            return true;
        }
        throw new RuntimeException('Authorization failed');
    }

    /**
     * Get permission keys of authenticated user.
     *
     * @return array
     */
    protected function getPermissionKeys(): array
    {
        return $this->session()->get('_PERMISSION_KEYS') ?: [];
    }
}
