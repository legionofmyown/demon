<?php
namespace Demon\Service;

class Log {
    public static function write($type, $message) {
        echo('[' . $type . '] ' . $message . "\n");
    }
}