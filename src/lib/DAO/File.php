<?php

namespace Onset\DAO;

class File
{
    private $fpReg = [];
    private function getFilePointer(string $path)
    {
        if (isset($this->fpReg[$path])) {
            return $this->fpReg[$path];
        }

        $mode = "";
        if (file_exists($path)) {
            $mode = 'r+';
        } else {
            $mode = 'x+';
        }
        return $this->fpReg[$path] = fopen($path, $mode);
    }

    public function readFile(string $path): string
    {
        $fp = $this->getFilePointer($path);
        flock($fp, LOCK_SH);
        $text = fread($fp, max(filesize($path), 1));
        rewind($fp);
        return $text;
    }

    public function writeFile(string $path, string $text): int
    {
        $fp = $this->getFilePointer($path);
        flock($fp, LOCK_EX);
        $byte = fwrite($fp, $text);
        flock($fp, LOCK_SH);
        return $byte;
    }

    public function readJsonFile(string $path)
    {
        return json_decode($this->readFile($path), true);
    }

    public function writeJsonFile(string $path, $data): int
    {
        return $this->writeFile($path, json_encode($data));
    }

    public function __destruct()
    {
        foreach ($this->fpReg as $fp) {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}
