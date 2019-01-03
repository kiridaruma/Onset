<?php

namespace Tests;

use Onset\DAO\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testRead()
    {
        $filepath = tempnam(sys_get_temp_dir(), 'file');
        $str = "hogefuga\nfoobar";
        file_put_contents($filepath, $str);
        $file = new File();
        $this->assertEquals($str, $file->readFile($filepath));
    }

    public function testReadJson()
    {
        $filepath = tempnam(sys_get_temp_dir(), 'file');
        $str = "765";
        file_put_contents($filepath, $str);
        $file = new File();
        $this->assertSame(765, $file->readJsonFile($filepath));
    }

    public function testWrite()
    {
        $filepath = tempnam(sys_get_temp_dir(), 'file');
        $str = "hogefuga\nfoobar";
        $file = new File();
        $file->writeFile($filepath, $str);
        $this->assertEquals($str, file_get_contents($filepath));
    }

    public function testWriteJson()
    {
        $filepath = tempnam(sys_get_temp_dir(), 'file');
        $str = "hogefuga\nfoobar";
        $file = new File();
        $file->writeJsonFile($filepath, [1,2,3]);
        $this->assertEquals("[1,2,3]", file_get_contents($filepath));
    }
}
