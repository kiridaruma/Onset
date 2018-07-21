<?php

class File
{
    private $fobj;
    public function __construct($path)
    {
        $this->fobj = new \SplFileObject($path, "w");
        $this->fobj->flock(LOCK_SH, $blocked);
    }

    public function read()
    {
        $fileSize = $this->fobj->getSize();
        return $this->fobj->fread($fileSize);
    }

    public function write($str)
    {
        $this->fobj->flock(LOCK_EX);
        $byte = $this->fobj->fwrite($str);
        $this->fobj->flock(LOCK_SH);

        return $byte;
    }

    public function __destruct()
    {
        $this->fobj->flock(LOCK_UN);
    }

    public static function remove($path)
    {
        $path = realpath($path);
        if (!file_exists($path)) {
            return;
        }

        switch (true) {
            case is_dir($path):
                foreach (scandir($path) as $child) {
                    if ($child === '.' || $child === '..') {
                        continue;
                    }
                    static::remove($path . '/' . $child);
                }
                break;

            case is_file($path):
            default:
                unlink($path);
        }
    }
}
