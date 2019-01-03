<?php

namespace Tests;

use Onset\Models\Room;
use PHPUnit\Framework\TestCase;

class RoomTest extends TestCase
{
    public function testCreateId()
    {
        $room = new Room();
        $this->assertEquals(56, mb_strlen($room->id));

        $room = new Room("hoge");
        $this->assertEquals("hoge", $room->id);
    }

    public function testGetChatlogAfter()
    {
        $room = new Room();
        $room->chatlog = [
            ['time' => 1],
            ['time' => 2],
            ['time' => 3],
            ['time' => 5],
            ['time' => 7],
            ['time' => 9],
        ];

        $this->assertEquals($room->chatlog, $room->getChatlogAfter(0));
        $this->assertEquals([['time' => 7], ['time' => 9]], $room->getChatlogAfter(5));
        $this->assertEquals([['time' => 9]], $room->getChatlogAfter(8));
        $this->assertEquals([], $room->getChatlogAfter(10));
    }
}
