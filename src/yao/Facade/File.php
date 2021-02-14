<?php


namespace Yao\Facade;

/**
 * @method static \Yao\File data(array $file)
 * @method static \Yao\File download($filename, $path)
 * Class File
 * @package Yao\Facade
 */
class File extends \Yao\Facade
{

    protected static function getFacadeClass()
    {
        return \Yao\File::class;
    }

}