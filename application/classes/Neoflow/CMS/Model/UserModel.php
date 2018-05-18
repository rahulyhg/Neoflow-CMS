<?php

namespace Neoflow\CMS\Model;

use InvalidArgumentException;
use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\Core\AbstractModel as FrameworkAbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Validation\Validator;
use RuntimeException;

class UserModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'users';

    /**
     * @var string
     */
    public static $primaryKey = 'user_id';

    /**
     * @var array
     */
    public static $properties = ['user_id', 'email', 'firstname', 'lastname',
        'role_id', 'reset_key', 'reseted_when', 'password',
        'failed_logins', ];

    /**
     * @var string
     */
    protected $newPassword = '';

    /**
     * @var string
     */
    protected $confirmPassword = '';

    /**
     * Get repository to fetch role.
     *
     * @return Repository
     */
    public function role(): Repository
    {
        return $this->belongsTo('Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }

    /**
     * Create user.
     *
     * @param array $data Data of user entity
     *
     * @return static
     */
    public static function create(array $data): FrameworkAbstractModel
    {
        $user = parent::create($data);

        if (isset($data['password']) && isset($data['confirmPassword'])) {
            $user->setNewPassword($data['password'], $data['confirmPassword']);
        }

        return $user;
    }

    /**
     * Update user by id.
     *
     * @param array $data Data for user
     * @param int   $id   Identifier of user
     *
     * @return static
     */
    public static function updateById(array $data, int $id): FrameworkAbstractModel
    {
        $user = parent::updateById($data, $id);

        if (isset($data['newPassword']) && isset($data['confirmPassword'])) {
            $user->setNewPassword($data['newPassword'], $data['confirmPassword']);
        }

        return $user;
    }

    /**
     * Validate user.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->email()
            ->callback(function ($email, $id) {
                return 0 === UserModel::repo()
                    ->where('email', '=', $email)
                    ->where('user_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', [$this->id()])
            ->set('email', 'Email address');

        $validator
            ->maxLength(50)
            ->set('firstname', 'Firstname');

        $validator
            ->maxLength(50)
            ->set('lastname', 'Lastname');

        $validator
            ->required()
            ->set('role_id', 'Role');

        if ($this->newPassword && $this->confirmPassword) {
            $this->validateNewPassword();
        }

        return (bool) $validator->validate();
    }

    /**
     * Verify password for login.
     *
     * @param string $password
     *
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        if ($password === $this->password) {
            $this
                ->setNewPassword($password, $password)
                ->save();

            return true;
        }

        return password_verify($password, $this->password);
    }

    /**
     * Validate new password of user.
     *
     * @return bool
     */
    public function validateNewPassword(): bool
    {
        $validator = new Validator([
            'newPassword' => $this->newPassword,
            'confirmPassword' => $this->confirmPassword,
            self::$primaryKey => $this->id(),
        ]);

        $validator
            ->required()
            ->set('confirmPassword', 'Confirm password');

        $validator
            ->required()
            ->minLength(8)
            //->pregMatch('/[a-z]+/', 'Password must contain at least one lowercase character')
            //->pregMatch('/[A-Z]+/', 'Password must contain at least one uppercase character')
            ->pregMatch('/[0-9]+/', 'Password must contain at least one number')
            ->pregMatch('/[\!\"\§\$\%\&\/\(\)\=\?\\\,\.\-\_\:\;\]\+\*\~\<\>\|]+/', 'Password must contain at least one special character')
            ->callback(function ($password, $confirmPassword) {
                return $password === $confirmPassword;
            }, 'Password is not matching confirm password', [$this->confirmPassword])
            ->set('newPassword', 'Password');

        return (bool) $validator->validate();
    }

    /**
     * Get fullname (firstname lastname).
     *
     * @return string
     */
    public function getFullname(): string
    {
        return trim($this->firstname.' '.$this->lastname);
    }

    /**
     * Get role.
     *
     * @return RoleModel|null
     */
    public function getRole(): RoleModel
    {
        return $this->role()->fetch();
    }

    /**
     * Save user.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        // Set new password
        if (mb_strlen($this->newPassword) > 0) {
            if ($this->newPassword === $this->confirmPassword) {
                $this->password = password_hash($this->newPassword, PASSWORD_BCRYPT, ['cost' => 8]);
            } else {
                throw new InvalidArgumentException('New password is not matching confirm password');
            }
        }

        return parent::save($preventCacheClearing);
    }

    /**
     * Delete user.
     *
     * @return bool
     */
    public function delete(): bool
    {
        // Prevent delete of initial user
        if (1 != $this->id()) {
            return parent::delete();
        }

        return false;
    }

    /**
     * Generate and set reset key.
     *
     * @return self
     */
    public function generateResetKey(): self
    {
        $this->reset_key = sha1(uniqid());
        $this->reseted_when = microtime(true);

        return $this;
    }

    /**
     * Reset reset key.
     *
     * @return self
     */
    public function deleteResetKey(): self
    {
        $this->reset_key = null;
        $this->reseted_when = null;

        return $this;
    }

    /**
     * Set new password.
     *
     * @param string $newPassword     New Password of user entity
     * @param string $confirmPassword Confirm password of user entity
     *
     * @return self
     */
    public function setNewPassword(string $newPassword, string $confirmPassword): self
    {
        $this->newPassword = $newPassword;
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    /**
     * Update password of user entity.
     *
     * @param string     $newPassword     New Password of user entity
     * @param string     $confirmPassword Confirm password of user entity
     * @param string|int $id              Identifier of user entity
     *
     * @return static
     *
     * @throws RuntimeException
     */
    public static function updatePassword(string $newPassword, string $confirmPassword, $id): self
    {
        $user = self::findById($id);
        if ($user) {
            return $user
                    ->setNewPassword($newPassword, $confirmPassword)
                    ->deleteResetKey();
        }
        throw new RuntimeException('Cannot update password of user (ID: '.$id.')');
    }
}
