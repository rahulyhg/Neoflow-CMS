<?php

namespace Neoflow\Validation;

use DateTime;

trait RuleTrait
{
    /**
     * Value has to match regular expression.
     *
     * @param string $regexPattern
     * @param string $message      Validation error message
     *
     * @return self
     */
    public function pregMatch(string $regexPattern, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $regexPattern = $args[0];

            return preg_match($regexPattern, $value);
        }, $message, [$regexPattern]);

        return $this;
    }

    /**
     * Value has to be a valid email address.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function email(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value) {
            return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
        }, $message);

        return $this;
    }

    /**
     * Value is required.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function required(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value) {
            return '' !== $value && null !== $value;
        }, $message);

        return $this;
    }

    /**
     * Value has to be a float number.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function float(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value) {
            return filter_var($value, FILTER_VALIDATE_FLOAT);
        }, $message);

        return $this;
    }

    /**
     * Value has to be an integer value.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function integer(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value) {
            return filter_var($value, FILTER_VALIDATE_INT);
        }, $message);

        return $this;
    }

    /**
     * Value consist only of digits.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function digits(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value) {
            return ctype_digit($value);
        }, $message);

        return $this;
    }

    /**
     * Value must be a number greater than or equal to X.
     *
     * @param int|float $limit   Validation limit
     * @param string    $message Validation error message
     *
     * @return self
     */
    public function min($limit, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $value = (float) $value;
            $limit = (float) $args[0];

            return $value >= $limit;
        }, $message, [$limit]);

        return $this;
    }

    /**
     * Value must be a number less than or equal to X.
     *
     * @param int|float $limit   Validation limit
     * @param string    $message Validation error message
     *
     * @return self
     */
    public function max($limit, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $value = (float) $value;
            $limit = (float) $args[0];

            return $value <= $limit;
        }, $message, [$limit]);

        return $this;
    }

    /**
     * Value must be a number between X and Y.
     *
     * @param int|float $minLimit Validiation minimal limit
     * @param int|float $maxLimit Validation maximal limit
     * @param string    $message  Validation error message
     *
     * @return self
     */
    public function between($minLimit, $maxLimit, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $value = (float) $value;
            $minLimit = (float) $args[0];
            $maxLimit = (float) $args[1];

            return $value >= $minLimit && $value <= $maxLimit;
        }, $message, [$minLimit, $maxLimit]);

        return $this;
    }

    /**
     * Value must have a string length greater than or equal to X.
     *
     * @param int    $length  Valdiation length
     * @param string $message Validation error message
     *
     * @return self
     */
    public function minLength(int $length, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $value = trim($value);
            $length = $args[0];

            return mb_strlen($value) >= $length;
        }, $message, [$length]);

        return $this;
    }

    /**
     * Value must have a string length less than or equal to X.
     *
     * @param int    $length  Valdiation length
     * @param string $message Validation error message
     *
     * @return self
     */
    public function maxLength(int $length, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $value = trim($value);
            $length = $args[0];

            return mb_strlen($value) <= $length;
        }, $message, [$length]);

        return $this;
    }

    /**
     * Value must have a string length between X and Y.
     *
     * @param int|float $minLimit Validiation minimal limit
     * @param int|float $maxLimit Validation maximal limit
     *
     * @return self
     */
    public function betweenLength($minLength, $maxLength, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $value = trim($value);
            $minLength = $args[0];
            $maxLength = $args[1];

            return mb_strlen($value) >= $minLength && mb_strlen($value) <= $maxLength;
        }, $message, [$minLength, $maxLength]);

        return $this;
    }

    /**
     * Value must have a string length of X.
     *
     * @param int    $length  Valdiation length
     * @param string $message Validation error message
     *
     * @return self
     */
    public function length(int $length, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $value = trim($value);
            $length = $args[0];

            return mb_strlen($value) === $length;
        }, $message, []);

        return $this;
    }

    /**
     * Value is the same as another value (password comparison etc).
     *
     * @param string $key     Key of matching value
     * @param string $label   Label of value
     * @param string $message Validation error message
     *
     * @return self
     */
    public function matches(string $key, string $label, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $match = $args[1];

            return $value === $match;
        }, $message, [$label, $this->get($key)]);

        return $this;
    }

    /**
     * Value is different from another value.
     *
     * @param string $key     Key of matching value
     * @param string $label   Label of value
     * @param string $message Validation error message
     *
     * @return self
     */
    public function notMatches(string $key, string $label, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $match = $args[1];

            return $value !== $match;
        }, $message, [$label, $this->get($key)]);

        return $this;
    }

    /**
     * Value must start with a specific string.
     *
     * @param string $sub
     * @param string $message Validation error message
     *
     * @return self
     */
    public function startsWith(string $sub, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $sub = $args[0];

            return 0 === mb_strpos($value, $sub);
        }, $message, [$sub]);

        return $this;
    }

    /**
     * Value must not start with a specific string.
     *
     * @param string $sub
     * @param string $message Validation error message
     *
     * @return self
     */
    public function notStartsWith($sub, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $sub = $args[0];

            return 0 !== mb_strpos($value, $sub);
        }, $message, [$sub]);

        return $this;
    }

    /**
     * Value must end with a specific string.
     *
     * @param string $sub
     * @param string $message Validation error message
     *
     * @return self
     */
    public function endsWith($sub, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $sub = $args[0];

            return mb_strpos($value, $sub) === mb_strlen($value) - mb_strlen($sub);
        }, $message, [$sub]);

        return $this;
    }

    /**
     * Value must not end with a specific string.
     *
     * @param string $sub
     * @param string $message Validation error message
     *
     * @return self
     */
    public function notEndsWith($sub, string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            $sub = $args[0];

            return mb_strpos($value, $sub) !== mb_strlen($value) - mb_strlen($sub);
        }, $message, [$sub]);

        return $this;
    }

    /**
     * Value has to be valid IP address.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function ip(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value) {
            return filter_var($value, FILTER_VALIDATE_IP);
        }, $message);

        return $this;
    }

    /**
     * Value has to be valid internet address.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function url(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($val) {
            return filter_var($val, FILTER_VALIDATE_URL);
        }, $message);

        return $this;
    }

    /**
     * Value has to be valid date.
     *
     * @param string $message Validation error message
     *
     * @return self
     */
    public function date(string $message = ''): self
    {
        $this->setRule(__FUNCTION__, function ($value, $args) {
            return (bool) DateTime::createFromFormat($this->translator()->getDateFormat(), $value);
        }, $message, [new DateTime('now')]);

        return $this;
    }

    /**
     * Value has to be a date later than or equal to X.
     *
     * @param string|int $limitDate       Limit date or number of days from today
     * @param string     $limitDateFormat Format of limit date or set null when number of days are given
     * @param string     $message         Validation error message
     *
     * @return self
     */
    public function minDate($limitDate, string $limitDateFormat, string $message = ''): self
    {
        // Set pre-rule to get a valid date
        $this->date();

        // Check if limit date is numeric as number of days from today
        if (is_numeric($limitDate)) {
            $limitDate = new DateTime($limitDate.' days');
        } else {
            $limitDate = DateTime::createFromFormat($limitDateFormat, $limitDate);
        }

        $this->setRule(__FUNCTION__, function ($value, $args) {
            $limitDate = $args[0];
            $value = DateTime::createFromFormat($this->translator()->getDateFormat(), $value);

            return $value && $limitDate <= $value;
        }, $message, [$limitDate]);

        return $this;
    }

    /**
     * Value has to be a date later than or equal to X.
     *
     * @param string|int $limitDate       Limit date or number of days from today
     * @param string     $limitDateFormat Format of limit date or set null when number of days are given
     * @param string     $message         Validation error message
     *
     * @return self
     */
    public function maxDate($limitDate, string $limitDateFormat, string $message = ''): self
    {
        // Set pre-rule to get a valid date
        $this->date();

        // Check if limit date is numeric as number of days from today
        if (is_numeric($limitDate)) {
            $limitDate = new DateTime($limitDate.' days');
        } else {
            $limitDate = DateTime::createFromFormat($limitDateFormat, $limitDate);
        }

        $this->setRule(__FUNCTION__, function ($value, $args) {
            $limitDate = $args[0];
            $value = DateTime::createFromFormat($this->translator()->getDateFormat(), $value);

            return $value && $limitDate >= $value;
        }, $message, [$limitDate]);

        return $this;
    }

    /**
     * Field has to be one of the allowed ones.
     *
     * @param string|array $allowed Allowed values
     * @param string       $message Validation error message
     *
     * @return FormValidator
     */
    public function oneOf($allowed, string $message = ''): self
    {
        if (is_string($allowed)) {
            $allowed = explode(',', $allowed);
        }

        $this->setRule(__FUNCTION__, function ($val, $args) {
            return in_array($val, $args[0]);
        }, $message, [$allowed]);

        return $this;
    }
}
