<?php

namespace Onset\Repositories;

use Onset\Models\Room;

abstract class RoomRepository
{
    abstract public function get(string $id): Room;
    abstract public function save(Room $room): void;
    abstract public function delete(Room $room): void;
    abstract public function gc(int $time): void;

    protected function build(array $data): Room
    {
        $room = new Room($data['id']);
        $room->chatlog = $data['chatlog'];
        $room->topic = $data['topic'];
        $room->memos = $data['memos'];
        return $room;
    }
}
