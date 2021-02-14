<?php

namespace {

    if (false === function_exists('db')) {
        /**
         * Db类助手函数
         * @param string $tableName
         * @return \Yao\Db
         */
        function db(string $tableName)
        {
            return \Yao\Facade\Db::name($tableName);
        }
    }

}

namespace Yao\Database {

    use Yao\Provider\Service;

    class DatabaseService implements Service
    {
        public function boot()
        {

        }

        public function register()
        {
            // TODO: Implement register() method.
        }

    }
}

