<?php
namespace Demon\Test\Service;

use Demon\Core\DB;
use Demon\Service\Api;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class CoreObjectTest extends \PHPUnit_Framework_TestCase
{

    public function testModulesOK()
    {
        $obj = \Demon\Module\Module::getClass('army');
        $this->assertEquals('army', $obj::$ID);
        $obj = \Demon\Module\Module::getClass('family');
        $this->assertEquals('family', $obj::$ID);
    }

    public function testFamilyAncestorOK()
    {
        $obj = \Demon\Module\Family\Ancestor::getClass('main');
        $this->assertEquals('main', $obj::$ID);
    }

    public function testArmyTroopOK()
    {
        $obj = \Demon\Module\Army\Troop::getClass('swordsman');
        $this->assertEquals('swordsman', $obj::$ID);
    }

}