<?php
namespace SoulFramework;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SoulException extends \Exception
{
    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    public static function catchException($exception)
    {
        // Log the exception.
        // TODO: Use a shared log object from the app.
        $logger = new Logger('confio-api');
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        $logger->error($exception);

        if ($exception->getCode() == 404) {
            if (class_exists('App\Controller\StaticController')) {
                $controller = new \App\Controller\StaticController();
                if (method_exists($controller, 'error404')) {
                    $controller->error404($exception);
                }
            }
        } else {
            if (DEBUG || php_sapi_name() == 'cli') {
                $message['code'] = $exception->getCode();
                $message['Message'] = $exception->getMessage();
                $message['File'] = $exception->getFile();
                $message['line'] = $exception->getLine();
                $message['trace'] = $exception->getTraceAsString();

                if (php_sapi_name() != 'cli') {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                    self::showError($message);
                } else {
                    self::showConsoleError($message);
                }
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                if (class_exists('App\Controller\StaticController')) {
                    $controller = new \App\Controller\StaticController();
                    if (method_exists($controller, 'error500')) {
                        $controller->error500($exception);
                    }
                }
            }
        }
    }
    public static function showError($message)
    {
        if ($_SERVER['HTTP_ACCEPT'] == 'application/json') {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . '500'. ' '. 'Internal Server Error', true, 500);
            echo json_encode(
                [
                    'code'      => $message['code'],
                    'message'   => $message['Message'],
                    'file'      => $message['File'],
                    'line'      => $message['line'],
                ]
            );
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "<h1> Error {$message['code']}</h1>";
            echo "<strong>{$message['Message']}</strong> ";
            echo "<br /><strong>file:</strong> {$message['File']} ";
            echo "<br /><strong>line:</strong> {$message['line']} ";
            echo "<br /><strong>trace:</strong>";
            foreach (explode("\n", $message['trace']) as $trace) {
                echo "<br />$trace";
            }
        }
    }

    public static function showConsoleError($message)
    {
        echo "\r\n Error {$message['code']}";
        echo "\r\n{$message['Message']}";
        echo "\r\nfile:</strong> {$message['File']}";
        echo "\r\nline:</strong> {$message['line']}\r\n";
    }
}
