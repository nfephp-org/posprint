<?php
/**
 * Class FileTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */

use Posprint\Connectors\File;

class FileTest extends PHPUnit_Framework_TestCase
{
    private $folderBase = '';
    private $filename = 'newfixture.dat';
    private $fakeData = array();
    private $connection;
    
    protected function setUp()
    {
        parent::setUp();
        $this->folderBase = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR;
        $this->fakeData = array(
            chr(13).chr(22).'abcdefg'.chr(0).chr(1),
            chr(13).chr(22).'hijklmnopqrstuvxz'.chr(2).chr(3),
            chr(13).chr(22).'abcdefghijklmnopqrstuvxz'.chr(5)
        );
        $filename = $this->folderBase . DIRECTORY_SEPARATOR. $this->filename;
        if (is_file($filename)) {
            unlink($filename);
        }
        $this->connection = new File($filename);
    }
    
    public function testWrite()
    {
        foreach ($this->fakeData as $data) {
            $this->connection->write($data);
        }
        $this->connection->close();
        
        $filename = $this->folderBase . DIRECTORY_SEPARATOR. $this->filename;
        $this->assertFileExists($filename);
        $newfixture = file_get_contents($filename);
        unlink($filename);
        $filename = $this->folderBase . DIRECTORY_SEPARATOR. 'fixturefile.dat';
        $fixture = file_get_contents($filename);
        $this->assertEquals($newfixture, $fixture);
    }
    
    public function testClose()
    {
        $this->connection->close();
        $this->assertFalse($this->connection->getFilepointer());
    }
}
