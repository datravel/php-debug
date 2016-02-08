<?php
namespace Debug;

use Psr\Log\LogLevel;

final class ErrorHandler
{
    /**
     * @var int
     */
    private $errorReporting;

    /**
     * @var array
     */
    private $errorLevelMap = array(
        E_ERROR             => LogLevel::CRITICAL,
        E_WARNING           => LogLevel::WARNING,
        E_PARSE             => LogLevel::ALERT,
        E_NOTICE            => LogLevel::NOTICE,
        E_CORE_ERROR        => LogLevel::CRITICAL,
        E_CORE_WARNING      => LogLevel::WARNING,
        E_COMPILE_ERROR     => LogLevel::ALERT,
        E_COMPILE_WARNING   => LogLevel::WARNING,
        E_USER_ERROR        => LogLevel::ERROR,
        E_USER_WARNING      => LogLevel::WARNING,
        E_USER_NOTICE       => LogLevel::NOTICE,
        E_STRICT            => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED        => LogLevel::NOTICE,
        E_USER_DEPRECATED   => LogLevel::NOTICE,
    );

    public function register($errorReporting)
    {
        $this->errorReporting = $errorReporting;
        register_shutdown_function([$this, 'handleShutdown']);
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError'], E_ALL);
    }

    public function handleShutdown()
    {
        $lastError = error_get_last();
        if (empty($lastError['type'])) {
            return;
        }
        $e = (new DebugException)
            ->setCode($lastError['type'])
            ->setFile($lastError['file'])
            ->setLine($lastError['line'])
            ->setMessage($lastError['message']);
        Log::critical($e);
    }

    /**
     * @param \Exception $e
     */
    public function handleException(\Exception $e)
    {
        Log::critical($e);
    }

    /**
     * @param int $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $extra
     * @throws DebugException if error type is E_RECOVERABLE_ERROR
     */
    public function handleError($type, $message, $file = '', $line = 0, array $extra = array())
    {
        if ($this->isErrorIgnored($type)) {
            return;
        }
        $e = (new DebugException)
            ->setCode($type)
            ->setFile($file)
            ->setLine($line)
            ->setMessage($message)
            ->setExtra($extra);
        $errorLevel = array_key_exists($type, $this->errorLevelMap) ? $this->errorLevelMap[$type] : LogLevel::CRITICAL;
        Log::log($errorLevel, $e);
        if ($type === E_RECOVERABLE_ERROR) {
            restore_error_handler();
            restore_exception_handler();
            throw new $e;
        }
    }

    /**
     * @param int $type
     * @return bool
     */
    private function isErrorIgnored($type)
    {
        if (!($type & $this->errorReporting)) {
            return true;
        } else {
            return false;
        }
    }
}