<?php


namespace Max\FileSystem;


class Operator
{
    public static function mkdir(string $dir, int $mode = 0777, bool $recursive = true)
    {
        if (file_exists($dir)) {
            if (is_file($dir)) {
                throw new \Exception('已经存在同名文件，不能创建文件夹!');
            }
        } else {
            mkdir($dir, $mode, $recursive);
        }
    }

    public static function modifyPath(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}