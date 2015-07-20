<?php
namespace Demon\Service;

use Demon\Exception\ApiException;
use Demon\Service\Api\ArmyApiService;
use Demon\Service\Api\HeroApiService;
use Demon\Service\Api\QuestApiService;
use Demon\Service\Api\UserApiService;
use Demon\Service\Api\MapApiService;

class Api {
    private static $instance = null;
    private $data = [];
    private $services = [];

    /**
     * @return Api
     */
    private static function instance() {
        if(self::$instance === null) {
            self::$instance = new Api;
            //TODO ? capture IP here?
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            self::$instance->data['remoteIP'] = $ip;
        }

        return self::$instance;
    }

    /**
     * @param string $service
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function request($service, $method, $args = []) {
        return self::instance()->getService($service, $method, $args);
    }

    private function __construct() {}
    private function __clone() {}

    private function getService($service, $method, $args) {
        if(!isset($this->services[$service])) {
            switch ($service) {
                case 'user':
                    $this->services['user'] = new UserApiService();
                    break;
                case 'hero':
                    $this->services['hero'] = new HeroApiService();
                    break;
                case 'map':
                    $this->services['map'] = new MapApiService();
                    break;
                case 'army':
                    $this->services['army'] = new ArmyApiService();
                    break;
                case 'quest':
                    $this->services['quest'] = new QuestApiService();
                    break;
                default:
                    throw new ApiException('API Service "' . $service . '" not found');
            }
        }

        /** @var \Demon\Service\Api\AbstractApiService $srv */
        $srv = $this->services[$service];

        return $srv->call(strtolower($method), $args);
    }

    public static function getData($key) {
        //TODO ? check if key exists
        return self::instance()->data[$key];
    }

    /*
    public static function setData($key, $value) {
        self::instance()->data[$key] = $value;
    }
    */

}