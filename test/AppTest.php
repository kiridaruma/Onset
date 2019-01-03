<?php

namespace Tests;

use Onset\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testConfig()
    {
        $config = ["hoge" => "fuga", "foo" => "bar"];
        $app = new App($config);

        $this->assertEquals($config, $app->config());
    }

    public function testContainer()
    {
        $config = ["hoge" => "fuga"];
        $app = new App($config);
        $app->bind("arrayModule", function (App $app): array{
            return $app->config();
        });
        $array = $app->resolve("arrayModule");
        $this->assertEquals($config, $array);
        $this->expectException(\RuntimeException::class);
        $app->resolve("unbinded key");
    }
}
