<?php
namespace Demon\Module\Map;

use Demon\Core\Model\Hero;
use Demon\Exception\GameException;
use Demon\Module\Map\Model\Link;
use Demon\Module\Map\Model\NodeObject;
use Demon\Module\Module;

class MapService extends Module {
    public static $ID = 'map';

    public static function getHeroData(Hero $hero) {
        //return map data
        $_links = $hero->getLinks();
        $links = Link::toArrayAll($_links);
        $nodes = [];

        foreach($_links as $link) {
            /** @var Link $link */
            $from = $link->getFromNode();
            $to = $link->getToNode();
            $nodes[$from::getID()] = static::getNodeData($from);
            $nodes[$to::getID()] = static::getNodeData($to);
        }

        $objects = $hero->getNodeObjects();
        foreach($objects as $object) {
            /** @var NodeObject $object */
            $node = $object->getNode();
            $nodes[$node::getID()]['objects'][] = $object->toArray();
        }

        return [
            'links' => $links,
            'nodes' => $nodes,
        ];
    }

    private static function getNodeData($node) {
        return [
            'node' => $node::getID(),
            //TODO change to translatable name getter
            'name' => $node::getName(),
            'group' => $node::getGroup(),
            'objects' => [],
        ];
    }

    public static function addLink(Hero $hero, $from, $to, $type) {
        /** @var Link $link */
        $link = Link::create();
        $link->setHero($hero);
        $link->setFromNode($from);
        $link->setToNode($to);
        $link->setType($type);

        if($hero->addLink($link)) {
            $link->save();
            return $link;
        }

        return null;
    }

    public static function addNodeObject(Hero $hero, $node, $object, $data = []) {
        $object = MapObject::getClass($object);
        $node = Node::getClass($node);

        $obj = NodeObject::create();
        /** @var NodeObject $obj */
        $obj->setHero($hero);
        $obj->setNode($node::getID());
        $obj->setObject($object::getID());
        $obj->setData($data);

        if($hero->addNodeObject($obj)) {
            $obj->save();
            return $obj;
        }

        return null;
    }

    public static function removeNodeObject(Hero $hero, $objectID) {
        /** @var NodeObject $object */
        $object = NodeObject::get($objectID);

        if($object->getHeroID() !== $hero->getID()) {
            throw new GameException('Node object ' . $object . ' doesn\'t belong to ' . $hero);
        }

        $hero->removeNodeObject($object);
        $object->delete();
    }

    public static function enter(Hero $hero, $node) {
        //TODO check if node is accessible

        $hero->setLastNode($node::getID());
        $hero->save();

        $ret = [];

        if(method_exists($node, 'onEnter')) {
            $ret = $node::onEnter($hero);
        }

        //TODO ? if node can't be entered?
        $ret['hero'] = $hero->toArray();

        return $ret;
    }

    public static function useObject(Hero $hero, $objectID) {
        /** @var NodeObject $object */
        $object = NodeObject::get($objectID);

        if($object->getHeroID() !== $hero->getID()) {
            throw new GameException('Node object ' . $object . ' doesn\'t belong to ' . $hero);
        }

        $obj = $object->getObject();
        if($obj::getGroup() !== 'item') {
            throw new GameException('Node object ' . $object . ' isn\'t usable');
        }

        $ret = [];

        if(method_exists($obj, 'onUse')) {
            $ret = $obj::onUse($hero, $objectID);
        }

        return $ret;
    }
}