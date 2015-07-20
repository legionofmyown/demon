<?php
namespace Demon\Service\Api;

abstract class AbstractApiService {

    abstract public function call($method, $args = []);

}