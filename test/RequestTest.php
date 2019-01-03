<?php

namespace Tests;

use Onset\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testQuery()
    {
        $data = ['hoge' => 'fuga'];
        $req = new Request($data, '');

        $this->assertEquals($data['hoge'], $req->query('hoge'));
        $this->assertNull($req->query('foo'));
        $this->assertEquals('piyo', $req->query('bar', 'piyo'));
    }

    public function testValue()
    {
        $data = ['hoge' => 'fuga'];
        $req = new Request([], json_encode($data));

        $this->assertEquals($data['hoge'], $req->value('hoge'));
        $this->assertNull($req->value('foo'));
        $this->assertEquals('piyo', $req->value('bar', 'piyo'));
    }
}
