<?php

require_once("class.php");
require_once("Log.php");

class ClassTest extends PHPUnit_Framework_TestCase
{
    function log($message){
        $logconf = ['mode'=>0777,'timeFormat'=>'%Y/%m/%d %H:%M:%S'];
        $log = Log::singleton('file', 'log/app.log', '', $logconf);
        $log->debug($message);
    }

    function testDropDB(){
        $db = new Db();
        $db->dropDB();
        $this->log("testDropDB");
        $this->assertTrue(true);
    }

    /*
     * @depends testDropDB
     */
    function testSetupDB(){
        $db = new Db();
        $db->setupDB();
        $this->log("testSetupDB");
        $this->assertTrue(true);
    }

    /*
     * @depends testSetupDB
     */
    function testAddCast(){
        $db = new Db();
        $actual = $db->addCast("aaaa");
        $this->assertEquals(1,$actual);
    }

    /*
     * @depends testSetupDB
     */
    function testAddMember(){
        $db = new Db();
        $actual = $db->addMember("1@example.com");
        $this->assertEquals(1,$actual);
    }

    /*
     * @depends testAddMember
     */
    function testListMembers(){
        $db = new Db();
        $db->addMember("2@example.com");
        $actual = $db->listMembers();
        $expected = [
            '1@example.com',
            '2@example.com'
        ];
        $this->log($actual);
        $this->assertEquals($expected,$actual);
    }

    /*
     * @depends testAddCast
     */
    function testListCasts(){
        $db = new Db();
        $db->addCast("bbbb");
        $expected = [
            'aaaa',
            'bbbb'
        ];
        $actual = $db->listCasts();
        $this->log($actual);
        $this->assertEquals($expected,$actual);
    }

    function testAddMathces(){
        $db = new Db();
        $actual = $db->addMatch("aaaa","1@example.com");
        $this->assertEquals(1,$actual);
    }

    function testListMatches(){
        $db = new Db();
        $db->addMatch("bbbb","2@example.com");
        $expected = [
            0 =>['name' => 'aaaa','mail' => '1@example.com'],
            1 =>['name' => 'bbbb','mail' => '2@example.com'],
        ];
        $actual = $db->listMatches();
        $this->log("testListMatches");
        $this->log($actual);
        $this->assertEquals($expected,$actual);
    }

    /*
     * @depends testListMatches
     */
    function testListMembersByCastName(){
        $db = new Db();
        $actual = $db->listMembersByCastName(['aaaa','bbbb']);
        $expected = ['1@example.com','2@example.com'];
        $this->assertEquals($expected,$actual);
    }


    /*
     * @depends testListMatches
     */
    function testListCastsByMember1(){
        $this->log("testListCastsByMember1");
        $db = new Db();
        $actual = $db->listCastsByMember(['1@example.com']);
        $expected = ['aaaa'];
        $this->assertEquals($expected,$actual);
    }

    /*
     * @depends testListMatches
     */
    function testListCastsByMember2(){
        $this->log("testListCastsByMember1");
        $db = new Db();
        $actual = $db->listCastsByMember(['1@example.com','2@example.com']);
        $expected = ['aaaa','bbbb'];
        $this->assertEquals($expected,$actual);
    }

    /*
     * @depends testAddCast
     */
    function testRemoveCast(){
        $db = new Db();
        $this->assertEquals(1,$db->removeCast("aaaa"));
    }

    /*
    * @depends testAddMember
    */
    function testRemoveMember(){
        $db = new Db();
        $this->assertEquals(1,$db->removeMember("1@example.com"));
    }

}