<?php

namespace Tests;

use Onset\DAO\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    use FileTestUtil;

    public function tearDown()
    {
        $this->refresh();
    }

    public function testRead()
    {
        $str = "hogefuga\nfoobar";
        $filepath = $this->createTempFile($str);
        $file = new File();
        $this->assertEquals($str, $file->readFile($filepath));
    }

    public function testReadJson()
    {
        $filepath = $this->createTempFile('765');
        $file = new File();
        $this->assertSame(765, $file->readJsonFile($filepath));
    }

    public function testWrite()
    {
        $str = "hogefuga\nfoobar";
        $filepath = $this->createTempFile($str);
        $file = new File();
        $file->writeFile($filepath, $str);
        $this->assertEquals($str, file_get_contents($filepath));
    }

    public function testWriteJson()
    {
        $filepath = $this->createTempFile('hogefugafoobar');
        $file = new File();
        $file->writeJsonFile($filepath, [1, 2, 3]);
        $this->assertEquals("[1,2,3]", file_get_contents($filepath));
    }
}
