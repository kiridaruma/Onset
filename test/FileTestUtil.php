<?php

namespace Tests;

trait FileTestUtil
{
    /**
     * テストで使用する一時的なフォルダのパス
     * このtraitの関数で作成されるリソースは、このパス以下に作られる
     */
    private $tempDir = null;

    /**
     * テストで使用するフォルダを作成する
     * このtraitの関数で作成されるリソースは、このパス以下に作られる
     */
    protected function tempDir(): string
    {
        if (!$this->tempDir) {
            $tempDir = sys_get_temp_dir() . '/onset_test';
            mkdir($tempDir);
            $this->tempDir = realpath($tempDir);
        }
        return $this->tempDir;
    }

    /**
     * テストで使用するフォルダ内でのパスから、絶対パスを返す
     * 存在しなければUnexpectedValueException
     *
     * @throws \UnexpectedValueException
     */
    private function resolvePath(string $path): string
    {
        $absPath = realpath($this->tempDir() . $path);
        if (!$absPath) {
            throw new \UnexpectedValueException('unexists path ' . $path);
        }
        return $absPath;
    }

    /**
     * テストで使用する一時的なファイルを作成する
     * 帰り値はファイルへのパス
     */
    protected function createTempFile(string $data = '', string $path = '/'): string
    {
        $basePath = $this->resolvePath($path);
        $filePath = tempnam($basePath, '');
        file_put_contents($filePath, $data);
        return $filePath;
    }

    /**
     * テストで使用する一時的なフォルダを作成する
     * 帰り値はフォルダへのパス
     */
    protected function createDir(string $path, string $name): string
    {
        $basePath = $this->resolvePath($path);
        $targetPath = $basePath . '/' . $name;
        mkdir($targetPath);
        return $targetPath;
    }

    /**
     * ファイルやフォルダを削除する
     * フォルダの場合、内部のデータも再帰的に削除する
     */
    protected function rm(string $dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->rm($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }

                }
            }
            rmdir($dir);
        }
    }

    /**
     * テストで使用した一時的なフォルダを削除する
     */
    protected function refresh(): void
    {
        $this->rm($this->tempDir);
    }
}
