<?php
namespace Demon\Exception;

class ClientException extends \Exception {
    private $clientMessage = '';

    public function __construct($clientMessage, $message = '', $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->clientMessage = $clientMessage;
    }

    public function getClientMessage() {
        return $this->clientMessage;
    }
}