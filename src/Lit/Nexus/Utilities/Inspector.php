<?php

declare(strict_types=1);

namespace Lit\Nexus\Utilities;

class Inspector
{
    protected const EXCEPTION_MESSAGE = <<<HTML
<h2>PHP Fatal error</h2>
<xmp>
%s
</xmp>
HTML;

    protected const THROWABLE_DESC = <<<TEXT
%s with message '%s' in %s:%s

Stack trace:
%s

thrown in %s on line %s
TEXT;

    protected const THROWABLE_DIVIDER = "\n\n******Previous******\n";

    public static function setGlobalHandler()
    {
        set_error_handler([static::class, 'errorHandler']);
        set_exception_handler([static::class, 'exceptionHandler']);
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return null;
        }

        throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
    }


    /**
     * @param \Exception $exception
     */
    public static function exceptionHandler($exception)
    {
        $msg = sprintf(
            static::EXCEPTION_MESSAGE,
            static::formatThrowable($exception)
        );

        if (php_sapi_name() !== 'cli' && !headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }

        echo $msg;
    }

    public static function formatThrowable(\Throwable $throwable)
    {
        $trace = $throwable->getTrace();

        $result = static::formatTrace($trace);

        $msg = sprintf(
            static::THROWABLE_DESC,
            get_class($throwable),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine(),
            implode("\n", $result),
            $throwable->getFile(),
            $throwable->getLine()
        );

        if ($previous = $throwable->getPrevious()) {
            $msg .= static::THROWABLE_DIVIDER . static::formatThrowable($previous);
        }

        return $msg;
    }

    public static function formatTrace($trace)
    {
        $result = array();
        $traceline = '#%s %4$s(%5$s) @ %2$s:%3$s';
        $key = 0;
        foreach ($trace as $key => $stackPoint) {
            if (isset($stackPoint['args'])) {
                foreach ($stackPoint['args'] as $k => $arg) {
                    unset($stackPoint['args'][$k]); //args下可能有引用，先unset防止串改
                    $stackPoint['args'][$k] = static::formatArg($arg);
                }
            } else {
                $stackPoint['args'] = array();
            }
            unset($arg);
            $fn = isset($stackPoint['class'])
                ? "{$stackPoint['class']}{$stackPoint['type']}{$stackPoint['function']}"
                : $stackPoint['function'];

            $result[] = sprintf(
                $traceline,
                $key,
                @$stackPoint['file'],
                @$stackPoint['line'],
                $fn,
                implode(', ', $stackPoint['args'])
            );
        }

        $result[] = '#' . ++$key . ' {main}';
        return $result;
    }

    protected static function formatArg($arg)
    {
        return is_scalar($arg) ? var_export($arg, true) : (is_object($arg) ? get_class(
            $arg
        ) : gettype($arg));
    }
}
