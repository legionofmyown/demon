<?php
namespace Demon\Service\Api;

use Demon\Exception\ApiException;
use Demon\Core\Model\User;
use Demon\Core\Model\Hero;
use Demon\Service\Api;

class UserApiService extends AbstractApiService {

    public function call($method, $args = []) {
        switch($method) {
            case 'auth':
                if(!isset($args['login']) || !isset($args['password'])) {
                    throw new ApiException('Method "Auth" from User service requires following fields: login, password');
                }
                return $this->auth($args['login'], $args['password']);
            case 'register':
                if(!isset($args['login']) || !isset($args['password'])) {
                    throw new ApiException('Method "Register" from User service requires following fields: login, password');
                }
                return $this->register($args['login'], $args['password']);
            case 'getuser':
                if(!isset($args['token'])) {
                    throw new ApiException('Method "getUser" from User service requires following fields: token');
                }
                return $this->getUser($args['token']);
            default:
                throw new ApiException('Method "' . $method . '" not found in User service');
        }
    }

    private function auth($login, $password) {
        /** @var User $user */
        $user = User::getOneBy('login', $login);
        if($user->verifyPassword($password)) {
            $token = uniqid('', true);
            $user->setCurrentToken($token);
            $user->setCurrentIP(Api::getData('remoteIP'));
            $user->save();
            return [
                'success' => true,
                'token' => $token,
                'user' => $user->toArray(),
            ];
        } else {
            return [
                'success' => false,
                'token' => '',
            ];
        }
    }

    private function register($login, $password) {
        /** @var User $user */
        $user = User::create();
        $user->setLogin($login);
        $user->setPassword($password);
        $user->save();

        return [
            'success' => true,
        ];
    }

    private function getUser($token) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);

        $ret = $user->toArray();
        $ret['heroes'] = Hero::toArrayAll($user->getHeroes());

        return $ret;
    }
}