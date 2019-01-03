<?php

namespace Tests;

use Onset\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testInit()
    {
        $room = new User();
        $this->assertTrue(isset($room->id));

        $room = new User('test');
        $this->assertEquals('test', $room->id);
    }
}
