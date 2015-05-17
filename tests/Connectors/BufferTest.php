<?php
/**
 * Class BufferTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */

use Posprint\Connectors\Buffer;

class BufferTest extends PHPUnit_Framework_TestCase
{
    private $fakeData = array();
    private $connection;
    
    protected function setUp()
    {
        parent::setUp();
        $this->fakeData = array(
            chr(13).chr(22).'abcdefg'.chr(0).chr(1),
            chr(13).chr(22).'hijklmnopqrstuvxz'.chr(2).chr(3),
            chr(13).chr(22).'abcdefghijklmnopqrstuvxz'.chr(5)
        );
        $this->connection = new Buffer();
    }
    
    public function loadFixture()
    {
        foreach ($this->fakeData as $data) {
            $this->connection->write($data);
        }
    }
    
    public function testWrite()
    {
        $this->loadFixture();
        $this->connection->close();
    }
    
    public function testGetDataReadable()
    {
        $this->connection->close();
        $this->loadFixture();
        $retArray = true; //return as array
        $aBin = $this->connection->getDataBinary($retArray);
        $fixture = array();
        foreach ($aBin as $data) {
            $fixture[] = $this->connection->friendlyBinary($data);
        }
        $data = $this->connection->getDataReadable($retArray);
        $this->connection->close();
        $this->assertEquals($data, $fixture);
    }
    
    public function testGetDataJson()
    {
        $this->connection->close();
        $this->loadFixture();
        $retArray = true; //return as array
        $aBin = $this->connection->getDataBinary($retArray);
        $fixture = json_encode($aBin);
        $data = $this->connection->getDataJson($retArray);
        $this->connection->close();
        $this->assertEquals($data, $fixture);
    }
    
    public function testGetDataBinary()
    {
        $this->connection->close();
        $this->loadFixture();
        $retArray = true; //return as array
        $data = $this->connection->getDataBinary($retArray);
        $this->connection->close();
        $this->assertEquals($data, $this->fakeData);
    }
    
    public function testClose()
    {
        $this->connection->close();
        $data = $this->connection->getDataBinary();
        $this->assertEquals($data, null);
    }
}
