<?php
namespace Demon\Service\Api;

use Demon\Exception\ApiException;
use Demon\Module\Quest\QuestService;
use Demon\Service\Api;
use Demon\Core\Model\User;

class QuestApiService extends AbstractApiService {

    public function call($method, $args = []) {
        switch($method) {
            case 'speaknpc':
                if(!isset($args['token']) || !isset($args['objectID'])) {
                    throw new ApiException('Method "Speaknpc" from Quest service requires following fields: token, objectID');
                }
                return $this->speaknpc($args['token'], $args['objectID']);
            default:
                throw new ApiException('Method "' . $method . '" not found in Quest service');
        }
    }

    private function speaknpc($token, $objectID) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);


        $ret = QuestService::speakNpc($user->getCurrentHero(), $objectID);

        return $ret;
    }
}