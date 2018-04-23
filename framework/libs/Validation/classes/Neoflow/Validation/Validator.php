<?php

namespace Neoflow\Validation;

use InvalidArgumentException;
use ReflectionFunction;

class Validator
{
    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * Rule trait.
     */
    use RuleTrait;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $labels = [];

    /**
     * @var array
     */
    protected $functions = [];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $validData = [];

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);

        $this->logger()->debug('Validator created', [
            'Data' => $data,
        ]);
    }

    /**
     * Set data.
     *
     * @param array $data Data for validation
     *
     * @return self
     */
    protected function setData(array $data): self
    {
        if (is_array($data)) {
            $this->data = $data;
        } elseif ($data instanceof AbstractModel) {
            $this->data = $data->toArray();
        } else {
            throw new InvalidArgumentException('Data is not an array or a model entity');
        }

        return $this;
    }

    /**
     * Callback.
     *
     * @param callable $callback     Validation callback
     * @param string   $message      Rule message
     * @param array    $callbackArgs Callback arguments
     *
     * @return Validator
     *
     * @throws InvalidArgumentException
     */
    public function callback(callable $callback, string $message = '', array $callbackArgs = []): self
    {
        if (is_callable($callback)) {
            // Generate generic and unique rule name
            $name = 'callback_'.sha1(uniqid('', true));

            // Set callback rule
            $this->setRule($name, function ($value) use ($callback, $callbackArgs) {
                // Add value to the callback arguments
                array_unshift($callbackArgs, $value);

                $reflection = new ReflectionFunction($callback);

                return $reflection->invokeArgs($callbackArgs);
            }, $message);
        } else {
            throw new InvalidArgumentException('Callback is not callable');
        }

        return $this;
    }

    /**
     * Set validation.
     *
     * @param string $key   Key of data value
     * @param string $label Label of data value
     *
     * @return self
     */
    public function set(string $key, string $label = ''): self
    {
        // Set and translate label for the error message
        if ($label) {
            $this->labels[$key] = translate($label, [], false, false);
        } else {
            $this->labels[$key] = $key;
        }

        // Get value by key
        $value = $this->get($key);

        // Validate value
        foreach ($this->rules as $rule => $status) {
            if ($status) {
                // Get rule function and arguments
                $function = $this->functions[$rule];
                $args = $this->arguments[$rule];

                // Run rule function
                $validationResult = is_array($args) ? $function($value, $args) : $function($value);

                // Check validation result
                if (!$validationResult) {
                    // Register error
                    $this->registerError($rule, $key);

                    // Reset rules
                    $this->rules = [];

                    return $this;
                }
            }
        }

        $this->validData[$key] = $value;

        // reset rules
        $this->rules = [];

        return $this;
    }

    /**
     * Check whether errors have been found.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * Get specific error by key.
     *
     * @param string $key Key of data value
     *
     * @return string
     */
    public function getErrorByKey(string $key): string
    {
        if (isset($this->errors[$key])) {
            return $this->errors[$key];
        }

        return null;
    }

    /**
     * Validate data.
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function validate(): array
    {
        // check for errors
        if ($this->hasErrors()) {
            $this->session()
                ->setNewFlash('validationErrors', $this->getErrors())
                ->setNewFlash('validationData', $this->data);

            $this->logger()->info('Validation failed', [
                'Errors' => $this->getErrors(),
            ]);

            throw new ValidationException('There were validation errors', $this->getErrors());
        }

        return $this->getValidData();
    }

    /**
     * Get all errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Returns valid data.
     *
     * @return array
     */
    public function getValidData(): array
    {
        return $this->validData;
    }

    /**
     * Get value.
     *
     * @param string $key Key of data value
     *
     * @return mixed
     */
    protected function get(string $key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Register error.
     *
     * @param string $rule    Name of rule
     * @param string $key     Key of data value
     * @param string $message Rule message
     *
     * return self
     */
    protected function registerError(string $rule, string $key, string $message = ''): self
    {
        // Initialize translation values
        $values = [$this->labels[$key]];

        // Define default message when empty
        if (empty($message)) {
            $message = $this->messages[$rule]['message'];
            $values = array_merge($values, $this->messages[$rule]['args']);
        }

        // Translate and set error message
        $this->errors[$key] = $this->translator()->translate($message, $values);

        return $this;
    }

    /**
     * Set rule.
     *
     * @param string   $rule     Name of rule
     * @param callable $function Rule function
     * @param string   $message  Rule message
     * @param array    $args     Arguments for anonymous rule function
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    protected function setRule(string $rule, callable $function, string $message = '', array $args = []): self
    {
        // Check if rule exists
        if (!isset($this->rules[$rule])) {
            // Activate rule for validation
            $this->rules[$rule] = true;

            $this->functions[$rule] = $function;
            $this->arguments[$rule] = $args;
            $this->messages[$rule] = [
                'message' => empty($message) ? $this->getDefaultMessage($rule) : $message,
                'args' => $args,
            ];
        }

        return $this;
    }

    /**
     * Get default error message.
     *
     * @param string $rule Name of rule
     *
     * @return string
     */
    protected function getDefaultMessage(string $rule): string
    {
        switch ($rule) {
            case 'email':
                $message = '{0} is an invalid email address';
                break;

            case 'ip':
                $message = '{0} is an invalid IP address';
                break;

            case 'url':
                $message = '{0} is an invalid url';
                break;

            case 'required':
                $message = '{0} is required';
                break;

            case 'float':
                $message = '{0} must consist of numbers only';
                break;

            case 'integer':
                $message = '{0} must consist of integer value';
                break;

            case 'digits':
                $message = '{0} must consist only of digits';
                break;

            case 'min':
                $message = '{0} must be greater than or equal to {1}';
                break;

            case 'max':
                $message = '{0} must be less than or equal to {1}';
                break;

            case 'between':
                $message = '{0} must be between {1} and {2}';
                break;

            case 'minLength':
                $message = '{0} must be at least {1} characters or longer';
                break;

            case 'maxLength':
                $message = '{0} must be no longer than {1} characters';
                break;

            case 'betweenLength':
                $message = '{0} must be between {1} and {2} characters';
                break;

            case 'length':
                $message = '{0} must be exactly {1} characters in length';
                break;

            case 'matches':
                $message = '{0} must match {1}';
                break;

            case 'notMatches':
                $message = '{0} must not match {1}';
                break;

            case 'startsWith':
                $message = '{0} must start with "{1}"';
                break;

            case 'notStartsWith':
                $message = '{0} must not start with "{1}"';
                break;

            case 'endsWith':
                $message = '{0} must end with "{1}"';
                break;

            case 'notEndsWith':
                $message = '{0} must not end with "{1}"';
                break;

            case 'date':
                $message = '{0} is not a valid date like {1}';
                break;

            case 'minDate':
                $message = '{0} must be later than {1}';
                break;

            case 'maxDate':
                $message = '{0} must be before {1}';
                break;

            case 'oneOf':
                $message = '{0} must be one of {1}';
                break;

            default:
                $message = '{0} has an error';
                break;
        }

        return $message;
    }
}
