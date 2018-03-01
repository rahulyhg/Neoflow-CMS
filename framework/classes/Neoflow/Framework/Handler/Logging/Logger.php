<?php

namespace Neoflow\Framework\Handler\Logging;

use DateTime;
use InvalidArgumentException;
use Neoflow\Framework\AppTrait;
use Neoflow\Filesystem\FileCollection;
use Neoflow\Filesystem\Folder;
use RuntimeException;
use Throwable;

class Logger
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var string
     */
    protected $logfilePath;

    /**
     * @var int
     */
    protected $loglevelThreshold = Loglevel::DEBUG;

    /**
     * @var int
     */
    protected $logLineCount = 0;

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * Constructor.
     *
     * @throws RuntimeException
     */
    public function __construct()
    {
        $logConfig = $this->config()->get('logger');
        $this->loglevelThreshold = strtoupper($logConfig->get('level'));

        $this->logfileDirectory = $this->config()->getLogsPath();
        if (!is_dir($this->logfileDirectory)) {
            mkdir($this->logfileDirectory, 077, true);
        }

        $this->logfilePath = $this->logfileDirectory.$logConfig->get('prefix').date('Y-m-d').'.'.$logConfig->get('extension');
        if (file_exists($this->logfilePath) && !is_writable($this->logfilePath)) {
            throw new RuntimeException('Log file "'.$this->logfilePath.'" is not writeable');
        }

        $this->fileHandle = fopen($this->logfilePath, 'a+');
        flock($this->fileHandle, LOCK_UN);
        if (!$this->fileHandle) {
            throw new RuntimeException('Log file "'.$this->logfilePath.'" could not be opened');
        }

        $this->debug('Logger created');
    }

    /**
     * Get log files.
     *
     * @param int $limit
     *
     * @return FileCollection
     */
    public function getLogfiles($limit = 10)
    {
        $logfileFolder = new Folder($this->logfileDirectory);
        $logfiles = $logfileFolder->findFiles('*.'.$this->config()->get('logger')->get('extension'));

        return $logfiles->slice($limit, 0);
    }

    /**
     * Get log level.
     *
     * @return string
     */
    public function getLoglevel()
    {
        return $this->loglevelThreshold;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }

    /**
     * Log message.
     *
     * @param int    $level
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function log($level, $message, array $context = [])
    {
        if (Loglevel::isValid($level)) {
            if (is_array($message)) {
                foreach ($message as $oneMessage) {
                    $this->log($level, $oneMessage, $context);
                }
            } else {
                if ($this->loglevelThreshold && array_search($this->loglevelThreshold, Loglevel::getLabels()) >= $level) {
                    $message = $this->formatMessage($level, $message, $context);
                    $this->write($message);
                }
            }

            return $this;
        }
        throw new InvalidArgumentException('Loglevel "'.$level.'" is not valid');
    }

    /**
     * Log error message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function error($message, $context = [])
    {
        return $this->log(Loglevel::ERROR, $message, $context);
    }

    /**
     * Log warning message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function warning($message, $context = [])
    {
        return $this->log(Loglevel::WARNING, $message, $context);
    }

    /**
     * Log info message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function info($message, $context = [])
    {
        return $this->log(Loglevel::INFO, $message, $context);
    }

    /**
     * Log debug message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function debug($message, $context = [])
    {
        return $this->log(Loglevel::DEBUG, $message, $context);
    }

    /**
     * Log exception as error.
     *
     * @param Throwable $ex
     *
     * @return Logger
     */
    public function logException(Throwable $ex)
    {
        $context = [
            'code' => $ex->getCode(),
            'file' => $ex->getFile(),
            'line' => $ex->getLine(),
            'url' => function_exists('request_url') ? request_url() : 'Unknown',
        ];

        if ($this->config()->get('logger')->get('stackTrace')) {
            $context['stack trace'] = get_exception_trace($ex, false, true);
        }

        return $this->error(get_class($ex).': '.$ex->getMessage(), $context);
    }

    /**
     * Writes a line to the log without prepending a status or timestamp.
     *
     * @param string $message Line to write to the log
     *
     * @return Logger
     */
    public function write($message)
    {
        if (null !== $this->fileHandle) {
            if (flock($this->fileHandle, LOCK_EX)) {
                if (false === fwrite($this->fileHandle, $message)) {
                    throw new RuntimeException('Log file "'.$this->logfilePath.'" is not writeable');
                } else {
                    $this->lastLine = trim($message);
                    ++$this->logLineCount;
                }
            }
            flock($this->fileHandle, LOCK_UN);
        }

        return $this;
    }

    /**
     * Get the log file path that the log is currently writing to.
     *
     * @return string
     */
    public function getLogfilePath()
    {
        return $this->logfilePath;
    }

    /**
     * Get the log file directory.
     *
     * @return string
     */
    public function getLogfileDirectory()
    {
        return $this->logfileDirectory;
    }

    /**
     * Get the last line logged to the log file.
     *
     * @return string
     */
    public function getLastLogLine()
    {
        return $this->lastLine;
    }

    /**
     * Formats the message for logging.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    protected function formatMessage($level, $message, $context)
    {
        if (!empty($context)) {
            $message .= PHP_EOL.$this->indent($this->contextToString($context));
        }

        return '['.$this->getTimestamp().'] ['.Loglevel::getLabel($level).'] '.$message.PHP_EOL;
    }

    /**
     * Gets the correctly formatted Date/Time for the log entry.
     *
     * PHP DateTime is dump, and you have to resort to trickery to get microseconds
     * to work correctly, so here it is.
     *
     * @return string
     */
    protected function getTimestamp()
    {
        $originalTime = microtime(true);
        $micro = sprintf('%06d', ($originalTime - floor($originalTime)) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));

        return $date->format('Y-m-d G:i:s.u');
    }

    /**
     * Takes the given context and coverts it to a string.
     *
     * @param array $context
     *
     * @return string
     */
    protected function contextToString($context)
    {
        $export = '';
        foreach ($context as $key => $value) {
            $export .= "{$key}: ";
            $export .= preg_replace(array(
                '/=>\s+([a-zA-Z])/im',
                '/array\(\s+\)/im',
                '/^  |\G  /m',
                ), array(
                '=> $1',
                '[]',
                '    ',
                ), str_replace('array (', 'array(', print_r($value, true)));
            $export .= PHP_EOL;
        }

        return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
    }

    /**
     * Indents the given string with the given indent.
     *
     * @param string $string
     * @param string $indent
     *
     * @return string
     */
    protected function indent($string, $indent = '    ')
    {
        return $indent.str_replace("\n", "\n".$indent, $string);
    }
}
