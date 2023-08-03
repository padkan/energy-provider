<?php

namespace App;

use App\Models\User;

class Flash {

    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';


    /**
     * @param string $message
     * @param string $type
     * 
     * @return [type]
     */
    public static function addMessage(string $message, string $type = 'success') {
        if (! isset($_SESSION['flash_notification'])) {
            $_SESSION['flash_notification'] = [];
        }
        $_SESSION['flash_notification'][] = [
            'body' => $message,
            'type' => $type,
        ];
    }

    public static function getMessages() {
        if (isset($_SESSION['flash_notification'])) {
            $messages = $_SESSION['flash_notification'];
            unset($_SESSION['flash_notification']);
            return $messages;
        }
    }

}