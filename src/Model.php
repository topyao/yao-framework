<?php

namespace Yao;

class Model
{
   
    public function __construct()
    {
        $this->name = rtrim(strchr(get_called_class(),'\\'),'\\');
    }


    public function __call($function_name,$arguments){
        return Db::name($this->name)->$function_name(...$arguments);
    }
}
