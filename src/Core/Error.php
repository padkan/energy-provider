<?php

namespace Core;

use App\Config;

 /**
  * class Error
  */
  class Error {
      

      /**
       * @param mixed $level
       * @param mixed $message
       * @param mixed $file
       * @param mixed $line
       * 
       * @return void
       */
      public static function errorHandler($level, $message, $file, $line) :void {
          if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
          }
      }

      /**
       * @param mixed $exception
       * 
       * @return void
       */
      public static function exceptionHandler($exception) :void {
        // handle code 404
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);

        if (Config::SHOW_ERROR) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaugth exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace: <pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in: '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            $log = dirname(__DIR__) . '\logs\\' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);
            $message = "<h1>Fatal error</h1>";
            $message .= "<p>Uncaugth exception: '" . get_class($exception) . "'</p>";
            $message .=  "<p>Message: '" . $exception->getMessage() . "'</p>";
            $message .=  "<p>Stack trace: <pre>" . $exception->getTraceAsString() . "</pre></p>";
            $message .=  "<p>Thrown in: '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
            error_log($message);
            //echo "<h1>An Error occured</h1>";
            BaseView::renderTemplate("$code.html");
        }

      }


  }