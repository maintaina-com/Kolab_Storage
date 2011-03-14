<?php
/**
 * Test the basic data handler.
 *
 * PHP version 5
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link       http://pear.horde.org/index.php?package=Kolab_Storage
 */

/**
 * Prepare the test setup.
 */
require_once dirname(__FILE__) . '/../../Autoload.php';

/**
 * Test the basic data handler.
 *
 * Copyright 2010-2011 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @category   Kolab
 * @package    Kolab_Storage
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link       http://pear.horde.org/index.php?package=Kolab_Storage
 */
class Horde_Kolab_Storage_Unit_Data_BaseTest
extends Horde_Kolab_Storage_TestCase
{
    public function testDefaultType()
    {
        $this->assertEquals(
            'event',
            $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->getType()
        );
    }

    public function testOverriddenType()
    {
        $this->assertEquals(
            'other',
            $this->getMessageStorage()
            ->getData('INBOX/Calendar', 'other')
            ->getType()
        );
    }

    public function testId()
    {
        $this->assertEquals(
            '988166e4fd2a5524aab076dae957fc59',
            $this->getMessageStorage()
            ->getData('INBOX/WithDeleted')
            ->getId()
        );
    }

    public function testStamp()
    {
        $this->assertEquals(
            'C:37:"Horde_Kolab_Storage_Folder_Stamp_Uids":86:{a:2:{i:0;a:2:{s:11:"uidvalidity";s:8:"12346789";s:7:"uidnext";i:5;}i:1;a:1:{i:0;i:4;}}}',
            serialize(
                $this->getMessageStorage()
                ->getData('INBOX/WithDeleted')
                ->getStamp()
            )
        );
    }

    public function testFetchPart()
    {
        $part = stream_get_contents(
            $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->fetchPart(1, '2')
        );
        $this->assertContains('<event', $part);
    }

    public function testFetch()
    {
        $objects = $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->fetch(array(1, 2, 4));
        $this->assertEquals('libkcal-543769073.139', $objects[4]['uid']);
    }

    public function testDataQueriable()
    {
        $data = $this->getMessageStorage()
            ->getData('INBOX/Calendar');
        $this->assertTrue($data instanceOf Horde_Kolab_Storage_Queriable);
    }

    public function testQuerySynchronization()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $data = $this->getMessageStorage()
            ->getData('INBOX/Calendar');
        $query = new Horde_Kolab_Storage_Stub_DataQuery(
            $data, array('factory' => $factory)
        );
        $data->registerQuery('stub', $query);
        $data->synchronize();
        $this->assertTrue($query->synchronized);
    }

    public function testGetQuery()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $data = $this->getMessageStorage()
            ->getData('INBOX/Calendar');
        $query = new Horde_Kolab_Storage_Stub_DataQuery(
            $data, array('factory' => $factory)
        );
        $data->registerQuery('Horde_Kolab_Storage_Stub_DataQuery', $query);
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Stub_DataQuery',
            $data->getQuery('Horde_Kolab_Storage_Stub_DataQuery')
        );
    }

    public function testGetBaseQuery()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $data = $this->getMessageStorage()
            ->getData('INBOX/Calendar');
        $query = new Horde_Kolab_Storage_Stub_DataQuery(
            $data, array('factory' => $factory)
        );
        $data->registerQuery(
            Horde_Kolab_Storage_Data::QUERY_BASE, $query
        );
        $this->assertInstanceOf(
            'Horde_Kolab_Storage_Stub_DataQuery',
            $data->getQuery()
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testRegisterInvalid()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $data = $this->getMessageStorage()
            ->getData('INBOX/Calendar');
        $data->registerQuery(
            Horde_Kolab_Storage_Data::QUERY_BASE,
            new Horde_Kolab_Storage_Stub_ListQuery(
                $this->getMessageStorage()
                ->getList(),
                array('factory' => $factory)
            )
        );
    }

    public function testGetObjects()
    {
        $this->assertType(
            'array',
            $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->getObjects()
        );
    }

    public function testObjects()
    {
        $objects = $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->getObjects();
        $this->assertEquals(
            'libkcal-543769073.139',
            $objects['libkcal-543769073.139']['uid']
        );
    }

    public function testGetObjectIds()
    {
        $this->assertType(
            'array',
            $this->getMessageStorage()->getData('INBOX/Calendar')->getObjectIds()
        );
    }

    public function testObjectIds()
    {
        $this->assertEquals(
            array('libkcal-543769073.139'),
            $this->getMessageStorage()->getData('INBOX/Calendar')->getObjectIds()
        );
    }

    public function testBackendId()
    {
        $this->assertEquals(
            '1',
            $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->getBackendId('libkcal-543769073.139')
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testMissingBackendId()
    {
        $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->getBackendId('NOSUCHOBJECT');
    }

    public function testExists()
    {
        $this->assertTrue(
            $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->objectIdExists('libkcal-543769073.139')
        );
    }

    public function testDoesNotExist()
    {
        $this->assertFalse(
            $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->objectIdExists('NOSUCHOBJECT')
        );
    }

    public function testGetObject()
    {
        $object = $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->getObject('libkcal-543769073.139');
        $this->assertEquals(
            'libkcal-543769073.139',
            $object['uid']
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testGetMissingObject()
    {
        $object = $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->getObject('NOSUCHOBJECT');
    }

    public function testCreateReturnsNull()
    {
        $this->assertNull(
            $this->getMessageStorage()
            ->getData('INBOX/Notes')
            ->create(array('desc' => 'test'))
        );
    }

    public function testFetchRaw()
    {
        $objects = $this->getMessageStorage()
            ->getData('INBOX/Calendar')
            ->fetch(array(1, 2, 4), true);
        $part = $objects[4]['content'];
        rewind($part);
        $this->assertContains('<uid>libkcal-543769073.139</uid>', stream_get_contents($part));
    }

    public function testCreateRaw()
    {
        $test = fopen('php://temp', 'r+');
        fputs($test, 'test');
        rewind($test);
        $this->assertNull(
            $this->getMessageStorage()
            ->getData('INBOX/Notes')
            ->create(array('content' => $test), true)
        );
    }

    public function testListAddedObjects()
    {
        $data = $this->getMessageStorage()->getData('INBOX/Notes');
        $data->create(array('desc' => 'test', 'uid' => 'UID'));
        $this->assertEquals(
            array('UID'),
            $data->getObjectIds()
        );
    }

    public function testDeleteObject()
    {
        $data = $this->getMessageStorage()->getData('INBOX/Notes');
        $data->create(array('desc' => 'test', 'uid' => 'UID'));
        $data->delete('UID');
        $this->assertEquals(
            array(),
            $data->getObjectIds()
        );
    }

    public function testDeleteAll()
    {
        $data = $this->getMessageStorage()->getData('INBOX/Notes');
        $data->create(array('desc' => 'test', 'uid' => 'UID1'));
        $data->create(array('desc' => 'test', 'uid' => 'UID2'));
        $data->deleteAll();
        $this->assertEquals(
            array(),
            $data->getObjectIds()
        );
    }

    public function testMoveObject()
    {
        $store = $this->getMessageStorage();
        $data = $store->getData('INBOX/Notes');
        $data->create(array('desc' => 'test', 'uid' => 'UID'));
        $data->move('UID', 'INBOX/OtherNotes');
        $other = $store->getData('INBOX/OtherNotes');
        $this->assertEquals(
            array(),
            $data->getObjectIds()
        );
        $this->assertEquals(
            array('UID'),
            $other->getObjectIds()
        );
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testModifyWithoutUid()
    {
        $store = $this->getMessageStorage();
        $data = $store->getData('INBOX/Notes');
        $data->create(array('desc' => 'test', 'uid' => 'UID'));
        $data->modify(array('desc' => 'test'));
    }

    /**
     * @expectedException Horde_Kolab_Storage_Exception
     */
    public function testModifyWithIncorrectUid()
    {
        $store = $this->getMessageStorage();
        $data = $store->getData('INBOX/Notes');
        $data->create(array('desc' => 'test', 'uid' => 'UID'));
        $data->modify(array('desc' => 'test', 'uid' => 'NOSUCHUID'));
    }

    public function testModify()
    {
        $store = $this->getMessageStorage();
        $data = $store->getData('INBOX/Notes');
        $data->create(array('desc' => 'test', 'uid' => 'UID'));
        $data->modify(array('desc' => 'modified', 'uid' => 'UID'));
        $object = $data->getObject('UID');
        $this->assertEquals('modified', $object['desc']);
    }
}
