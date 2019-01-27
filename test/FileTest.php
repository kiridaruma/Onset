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
        $filepath = $this->createTempFile('/read_test', $str);
        $file = new File();
        $this->assertEquals($str, $file->readFile($filepath));
    }

    public function testReadJson()
    {
        $filepath = $this->createTempFile('/read_json_test', '765');
        $file = new File();
        $this->assertSame(765, $file->readJsonFile($filepath));
    }

    public function testWrite()
    {
        $str = "hogefuga\nfoobar";
        $filepath = $this->createTempFile('/write_test', $str);
        $file = new File();
        $file->writeFile($filepath, $str);
        $this->assertEquals($str, file_get_contents($filepath));
    }

    public function testWriteJson()
    {
        $filepath = $this->createTempFile('/write_json_test', 'hogefugafoobar');
        $file = new File();
        $file->writeJsonFile($filepath, [1, 2, 3]);
        $this->assertEquals("[1,2,3]", file_get_contents($filepath));
    }

    public function testWriteAndRead()
    {
        $filepath = $this->tempDir() . '/hoge';
        $file = new File();
        $str = "hogefugapiyo\nfoobarbaz";
        $file->writeFile($filepath, $str);
        $res = $file->readFile($filepath);
        $this->assertEquals($str, $res);
    }
}
