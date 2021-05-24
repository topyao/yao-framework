<?php

namespace Max\Console\Commands;

use Max\Console\Command;

class Config extends Command
{
    public function out()
    {
        self::Load();
    }

    public static function Load()
    {
        $installed = json_decode(file_get_contents(getcwd() . '/vendor/composer/installed.json'), true);
        foreach ($installed['packages'] as $package) {
            if (isset($package['extra']['max']['config'])) {
                $name = ltrim(strrchr($package['name'], '/'), '/');
                if (false === file_exists($config = getcwd() . '/config/' . $name . '.php')) {
                    if (copy(getcwd() . '/vendor/max/' . $name . '/src/' . $name . '.php', $config)) {
                        echo "\033[32m Generate config file successfully: /config/{$name}.php \033[0m \n";
                    }
                }
            }
        }

    }

}