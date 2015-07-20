<?php
namespace Demon\Service\Api;

use Demon\Core\Model\Character;
use Demon\Exception\ApiException;
use Demon\Module\Army\ArmyService;
use Demon\Module\Army\Troop;
use Demon\Service\Api;
use Demon\Core\Model\User;

class ArmyApiService extends AbstractApiService {

    public function call($method, $args = []) {
        switch($method) {
            case 'createsquad':
                if(!isset($args['token']) || !isset($args['character']) || !isset($args['troop']) || !isset($args['number'])) {
                    throw new ApiException('Method "Createsquad" from Army service requires following fields: token, character, troop, number');
                }
                return $this->createsquad($args['token'], $args['character'], $args['troop'], $args['number']);
            case 'attackneutral':
                if(!isset($args['token']) || !isset($args['squads']) || !isset($args['objectID'])) {
                    throw new ApiException('Method "Attackneutral" from Army service requires following fields: token, squads, objectID');
                }
                return $this->attackneutral($args['token'], $args['squads'], $args['objectID']);
            default:
                throw new ApiException('Method "' . $method . '" not found in Army service');
        }
    }

    private function createsquad($token, $character, $troop, $number) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);

        /** @var Character $character */
        $character = Character::get($character);

        $ret = ArmyService::createSquad($user->getCurrentHero(), $character, $troop, $number);

        return $ret;
    }

    private function attackneutral($token, $squads, $objectID) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);

        if(!is_array($squads) || !count($squads)) {
            throw new ApiException('Method "Attackneutral" argument "squads" must be a non-empty array');
        }

        $ret = ArmyService::attackNeutral($user->getCurrentHero(), $squads, $objectID);

        return $ret;
    }
}