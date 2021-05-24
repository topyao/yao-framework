<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static \Max\File data(array $file)
 * @method static \Max\File download($filename, $path)
 * Class File
 * @package Max\Facade
 */
class File extends Facade
{

    protected static $singleInstance = false;

    protected static function getFacadeClass()
    {
        return 'file';
    }

}