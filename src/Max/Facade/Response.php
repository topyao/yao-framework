<?php
declare(strict_types=1);

namespace Max\Facade;

/**
 * @method static \Max\Http\Response body(int|array|string|\Closure|\Max\Http\Response $body)
 * @method static \Max\Http\Response code(int $code)
 * @method static \Max\Http\Response redirect(string $url, int $code = 302)
 * @method static \Max\Http\Response header(string|array $header)
 * Class Config
 * @package Max\Facade
 */
class Response extends Facade
{

    protected static function getFacadeClass()
    {
        return 'response';
    }
}
