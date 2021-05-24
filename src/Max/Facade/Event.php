<?php
declare(strict_types=1);

namespace Max\Facade;


/**
 * @method static trigger($trigger);
 * Class Event
 * @package Max\Facade
 */
class Event extends Facade
{

    protected static function getFacadeClass()
    {
        return 'event';
    }

}