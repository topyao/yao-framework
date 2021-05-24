<?php
declare(strict_types=1);

namespace Max\Console\Commands;

use Max\Console\Command;
use Max\Tools\File;

class Make extends Command
{

    private $optionsMap = [
        1 => 'controller',
        2 => 'model',
        3 => 'middleware'
    ];

    public function out()
    {
        echo <<<EOT
(1). 控制器
(2). 模型
(3). 中间件
输入要生成的文件<1,2,3>：
EOT;
        fscanf(STDIN, '%d', $options);
        if (array_key_exists($options, $this->optionsMap)) {
            return call_user_func([$this, $this->optionsMap[$options]]);
        }
        return $this->out();
    }

    public function controller()
    {

        echo <<<EOT
(1). 普通控制器
(2). 资源控制器
选择控制器类型:
EOT;
        $controllTemplatePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'skeleton' . DIRECTORY_SEPARATOR;
        fscanf(STDIN, '%d', $type);
        switch ($type) {
            case 1:
                $file = $controllTemplatePath . 'controller.tpl';
                break;
            case 2:
                $file = $controllTemplatePath . 'controller_rest.tpl';
                break;
            default:
                return $this->controller();
        }
        $this->writeLine('输入控制器：');
        fscanf(STDIN, '%s', $behavior);

        $array = explode('/', $behavior);

        $controller = ucfirst(array_pop($array));

        $namespace = implode('\\', array_map(function ($value) {
            return ucfirst($value);
        }, $array));

        $path = env('app_path') . 'Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;

        $controllerFile = $path . $controller . '.php';
        if (file_exists($controllerFile)) {
            return $this->writeLine("控制器已经存在!\n");
        }

        File::mkdir($path);

        $file = str_replace(['{{namespace}}', '{{class}}'], ['App\\Http\\Controllers\\' . $namespace, $controller], file_get_contents($file));
        file_put_contents($path . $controller . '.php', $file);
        return $this->writeLine('控制器App\\Http\\Controllers\\' . $namespace . '\\' . $controller . "创建成功！\n");
    }


    public function model()
    {
        return $this->writeLine("暂时不支持创建模型！\n");
    }

    public function middleware()
    {
        return $this->writeLine("暂时不支持创建中间件！\n");
    }
}
