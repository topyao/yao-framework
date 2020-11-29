<?php


namespace yao\facade;


use yao\Facade;

/**
 * @method static \yao\File data(array $file)
 * @method static \yao\File download($filename, $path)
 * Class File
 * @package yao\facade
 */
class File extends Facade
{

    protected static function getFacadeClass()
    {
        return \yao\File::class;
    }

}