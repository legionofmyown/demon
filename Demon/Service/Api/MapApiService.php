<?php
namespace Demon\Service\Api;

use Demon\Core\Model\Hero;
use Demon\Exception\ApiException;
use Demon\Module\Map\MapService;
use Demon\Module\Map\Model\NodeObject;
use Demon\Module\Map\Node;
use Demon\Service\Api;
use Demon\Core\Model\User;

class MapApiService extends AbstractApiService {

    public function call($method, $args = []) {
        switch($method) {
            case 'enter':
                if(!isset($args['token']) || !isset($args['node'])) {
                    throw new ApiException('Method "Enter" from Map service requires following fields: token, node');
                }
                return $this->enter($args['token'], $args['node']);
            case 'useobject':
                if(!isset($args['token']) || !isset($args['objectID'])) {
                    throw new ApiException('Method "Useobject" from Map service requires following fields: token, objectID');
                }
                return $this->useobject($args['token'], $args['objectID']);
            default:
                throw new ApiException('Method "' . $method . '" not found in Map service');
        }
    }

    private function enter($token, $node) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);

        $node = Node::getClass($node);

        $ret = MapService::enter($user->getCurrentHero(), $node);

        return $ret;
    }

    private function useobject($token, $objectID) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);

        $ret = MapService::useObject($user->getCurrentHero(), $objectID);

        return $ret;
    }

}