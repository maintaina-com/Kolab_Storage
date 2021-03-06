<?php
/**
 * Tests the synchronisation handler.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

/**
 * Tests the synchronisation handler.
 *
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Horde_Kolab_Storage_Unit_List_Synchronization_BaseTest
extends PHPUnit_Framework_TestCase
{
    public function testSynchronize()
    {
        $synchronization = new Horde_Kolab_Storage_List_Synchronization_Base();
        $listener = $this->getMock('Horde_Kolab_Storage_List_Synchronization_Listener');
        $listener->expects($this->once())
            ->method('synchronize');
        $synchronization->registerListener($listener);
        $synchronization->synchronize();
    }
}
