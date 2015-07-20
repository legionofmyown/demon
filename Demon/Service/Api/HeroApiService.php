<?php
namespace Demon\Service\Api;

use Demon\Core\Model\Hero;
use Demon\Exception\ApiException;
use Demon\Module\Army\ArmyService;
use Demon\Module\Family\Ancestor;
use Demon\Module\Family\FamilyService;
use Demon\Module\Inventory\InventoryService;
use Demon\Module\Inventory\Item\Unit\Brigand;
use Demon\Module\Map\LinkType\Visible;
use Demon\Module\Map\MapService;
use Demon\Module\Map\Node\Island\Overview;
use Demon\Module\Map\Node\Start\Cave;
use Demon\Module\Map\Node\Start\Mountain;
use Demon\Module\Module;
use Demon\Module\Quest\QuestService;
use Demon\Service\Api;
use Demon\Core\Model\User;

class HeroApiService extends AbstractApiService {

    public function call($method, $args = []) {
        switch($method) {
            case 'create':
                if(!isset($args['token']) || !isset($args['name'])) {
                    throw new ApiException('Method "Create" from Hero service requires following fields: token, name');
                }
                return $this->create($args['token'], $args['name']);
            case 'choose':
                if(!isset($args['token']) || !isset($args['id'])) {
                    throw new ApiException('Method "Choose" from Hero service requires following fields: token, id');
                }
                return $this->choose($args['token'], $args['id']);
            default:
                throw new ApiException('Method "' . $method . '" not found in Hero service');
        }
    }

    private function create($token, $name) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);

        //TODO check rules and unique name
        /** @var Hero $hero */
        $hero = Hero::create();
        $hero->setName($name);
        $hero->setUser($user);
        $hero->addModule(FamilyService::getID());
        //$hero->addModule(ArmyService::getID());
        $hero->addModule(MapService::getID());
        //$hero->addModule(QuestService::getID());
        //$hero->addModule(InventoryService::getID());
        $hero->setLastNode(Cave::getID());
        $hero->save();

        //TODO ? another name
        FamilyService::addCharacter($hero, Ancestor\Main::getID(), $name);

        MapService::addLink($hero, Cave::getID(), Overview::getID(), Visible::getID());
        //MapService::addLink($hero, Cave::getID(), Mountain::getID(), Visible::getID());

        //InventoryService::modItemNumber($hero, Brigand::getID(), 50);

        $user->addHero($hero);

        return $hero->toArray();
    }

    private function choose($token, $id) {
        /** @var User $user */
        $user = User::getOneBy('token', [$token, Api::getData('remoteIP')]);

        $hero = null;
        $heroes = $user->getHeroes();
        foreach($heroes as $chero) {
            /** @var Hero $chero */
            if($chero->getID() == $id) {
                $hero = $chero;
                break;
            }
        }

        if($hero === null) {
            throw new ApiException('User has no hero with ID ' . $id);
        }

        $user->setCurrentHero($hero);
        $user->save();

        return $this->createHeroData($hero);
    }

    private function createHeroData(Hero $hero) {
        QuestService::checkQuests($hero);
        $data = [];
        $data['hero'] = $hero->toArray();
        $data['modules'] = [];
        $modules = $hero->getModules();
        foreach($modules as $module) {
            $mod = Module::getClass($module);
            $data['modules'][$module] = $mod::getHeroData($hero);
        }

        return $data;
    }

}