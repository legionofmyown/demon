<?php
namespace Demon\Module\Map\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Core\Model\Hero;
use Demon\Exception\DataException;
use Demon\Module\Map\LinkType;
use Demon\Module\Map\Node;

class Link extends DataObject {

    protected static $_dbRequests = [];
    protected $hero;
    protected $heroID;
    protected $fromNode;
    protected $toNode;
    protected $type;

    public static function dbTableName()
    {
        return 'link';
    }

    public static function dbTableFields()
    {
        return ['ID', 'heroID', 'fromNode', 'toNode', 'type'];
    }

    public static function dbRequest($request)
    {
        if (!parent::dbRequest($request)) {
            $joinedFields = join(',', static::dbTableFields());
            switch ($request) {
                case 'load-heroID':
                    static::$_dbRequests['load-heroID'] = DB::getInstance()->prepare('SELECT ' . $joinedFields . ' FROM `' . static::dbTableName() . '` WHERE `heroID` = ?');
                    break;
                default:
                    throw new DataException('Unknown request "' . $request . '"');
            }
        }

        return static::$_dbRequests[$request];
    }

    public function setHero(Hero $hero) {
        $this->hero = $hero;
        $this->heroID = $hero->getID();
    }

    public function getFromNode() {
        //TODO ? specific field?
        return Node::getClass($this->fromNode);
    }

    public function setFromNode($node) {
        $node = Node::getClass($node);
        $this->fromNode = $node::getID();
    }

    public function getToNode() {
        //TODO ? specific field?
        return Node::getClass($this->toNode);
    }

    public function setToNode($node) {
        $node = Node::getClass($node);
        $this->toNode = $node::getID();
    }

    public function getType() {
        //TODO ? specific field?
        return LinkType::getClass($this->type);
    }

    public function setType($type) {
        $type = LinkType::getClass($type);
        $this->type = $type::getID();
    }

    public function __toString() {
        //TODO correct naming
        return '(#' . $this->getID() . ')';
    }

    public function toArray() {
        return [
            'ID' => $this->getID(),
            'fromNode' => $this->fromNode,
            'toNode' => $this->toNode,
            'type' => $this->type,
        ];
    }

}