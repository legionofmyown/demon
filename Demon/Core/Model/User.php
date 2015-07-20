<?php
namespace Demon\Core\Model;

use Demon\Core\DataObject;
use Demon\Core\DB;
use Demon\Exception\ClientException;
use Demon\Exception\DataException;
use Demon\Exception\DataNotFoundException;
use Demon\Exception\MultipleDataException;

class User extends DataObject
{
    protected static $_dbRequests = [];
    protected $login = null;
    protected $password = null;
    protected $currentIP = null;
    protected $currentToken = null;
    /** @var Hero|null $currentHero */
    protected $currentHero = null;
    protected $currentHeroID = null;
    protected $heroes = null;

    public static function dbTableName()
    {
        return 'user';
    }

    public static function dbTableFields()
    {
        return ['ID', 'login', 'password', 'currentIP', 'currentToken', 'currentHeroID'];
    }

    public static function dbRequest($request)
    {
        if (!parent::dbRequest($request)) {
            $joinedFields = join(',', static::dbTableFields());
            switch ($request) {
                case 'load-login':
                    static::$_dbRequests['load-login'] = DB::getInstance()->prepare('SELECT ' . $joinedFields . ' FROM `' . static::dbTableName() . '` WHERE `login` = ?');
                    break;
                case 'load-token':
                    static::$_dbRequests['load-token'] = DB::getInstance()->prepare('SELECT ' . $joinedFields . ' FROM `' . static::dbTableName() . '` WHERE `currentToken` = ? AND `currentIP` = ?');
                    break;
                default:
                    throw new DataException('Unknown request "' . $request . '"');
            }
        }

        return static::$_dbRequests[$request];
    }

    public function setLogin($login)
    {
        //verify unique
        try {
            $user = User::getOneBy('login', $login);
        } catch(DataNotFoundException $e) {
            $user = null;
        }

        if($user !== null && $user !== $this) {
            throw new ClientException('Login already exists.');
        }

        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getCurrentIP() {
        return $this->currentIP;
    }

    public function setCurrentIP($ip) {
        $this->currentIP = $ip;
    }

    public function getCurrentToken() {
        return $this->currentToken;
    }

    public function setCurrentToken($token) {
        $this->currentToken = $token;
    }

    /**
     * @return Hero
     */
    public function getCurrentHero() {
        if($this->currentHero === null) {
            $this->currentHero = Hero::get($this->currentHeroID);
        }
        return $this->currentHero;
    }

    public function setCurrentHero(Hero $hero) {
        $this->currentHero = $hero;
        $this->currentHeroID = $hero->getID();
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function getHeroes() {
        if($this->heroes === null) {
            $this->heroes = Hero::getAllBy('userID', $this->ID);
        }
        return $this->heroes;
    }

    public function addHero(Hero $hero) {
        $this->getHeroes();
        if(!in_array($hero, $this->heroes)) {
            $this->heroes[] = $hero;
        }
    }

    public function __toString()
    {
        return $this->getLogin() . ' (#' . $this->getID() . ')';
    }

    public function toArray() {
        return [
            'ID' => $this->getID(),
            'login' => $this->getLogin(),
        ];
    }

}