<?php

namespace Onset\Models;

class Room implements \JsonSerializable
{
    public $id;
    public $chatlog = [];
    public $topic = '';
    public $memos = [];
    public function __construct(string $id = '')
    {
        $this->id = $id === '' ? hash('sha3-224', mt_rand() . time()) : $id;
    }

    public function getChatlogAfter(int $time): array
    {
        if (count($this->chatlog) === 0) {
            return [];
        }

        $cursor = count($this->chatlog) - 1;
        while ($cursor > 0) {
            if ($this->chatlog[$cursor]['time'] <= $time) {
                $cursor += 1;
                break;
            }
            $cursor -= 1;
        }
        return array_slice($this->chatlog, $cursor);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'chatlog' => $this->chatlog,
            'topic' => $this->topic,
            'memos' => $this->memos,
            'users' => $this->users,
        ];
    }
}
